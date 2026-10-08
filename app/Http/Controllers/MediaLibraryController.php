<?php

namespace App\Http\Controllers;

use App\Models\{GalleryItem,MediaAsset,SiteSetting};
use App\Support\UploadImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class MediaLibraryController extends Controller {
    private const CATEGORIES = ['سالن عروسی','تولد','عقد','نامزدی','پذیرایی و غذا','دکوراسیون','محوطه'];

    public function store(Request $request) {
        $data=$request->validate([
            'images'=>'required|array|min:1|max:12',
            'images.*'=>'required|image|mimes:jpg,jpeg,png,webp|max:8192',
            'category'=>'required|string|max:80',
            'title'=>'nullable|string|max:150',
            'is_active'=>'nullable|boolean',
        ]);
        if(!in_array($data['category'],self::CATEGORIES,true)) {
            throw ValidationException::withMessages(['category'=>'دسته‌بندی تصویر معتبر نیست.']);
        }
        $n=0;
        foreach($request->file('images') as $image) {
            $url=UploadImage::store($image,'gallery');
            if(!$url) continue;
            $this->register($url,$data['category'],($data['title']??null)?:pathinfo($image->getClientOriginalName(),PATHINFO_FILENAME),
                'uploaded', (int)$image->getSize(), @getimagesize($image->getRealPath()) ?: null, $request->boolean('is_active',true));
            $n++;
        }
        return back()->with('success', "تعداد {$n} تصویر به مدیا لایبرری افزوده شد.")
            ->withFragment('admin-media');
    }

    // Import only images from a ZIP, never trust paths from the archive.
    public function importZip(Request $request) {
        $request->validate([
            'archive'=>'required|file|mimes:zip|max:51200',
            'category'=>'required|string|max:80',
            'replace_demo'=>'nullable|boolean',
        ]);
        $category=$request->input('category');
        if(!in_array($category,self::CATEGORIES,true)) {
            throw ValidationException::withMessages(['category'=>'دسته‌بندی تصویر معتبر نیست.']);
        }
        if(!class_exists(ZipArchive::class)) {
            throw ValidationException::withMessages(['archive'=>'افزونه ZIP روی سرور فعال نیست.']);
        }
        $zip=new ZipArchive();
        if($zip->open($request->file('archive')->getRealPath())!==true) {
            throw ValidationException::withMessages(['archive'=>'فایل ZIP قابل خواندن نیست.']);
        }
        $manifest=[];
        $manifestIndex=$zip->locateName('manifest.json',ZipArchive::FL_NOCASE);
        if($manifestIndex!==false) {
            $metadata=json_decode((string)$zip->getFromIndex($manifestIndex),true);
            foreach(($metadata['photos']??[]) as $photo) {
                if(!empty($photo['file']) && in_array(($photo['category']??''),self::CATEGORIES,true)) {
                    $manifest[basename($photo['file'])]=[
                        'category'=>$photo['category'],
                        'title'=>mb_substr((string)($photo['title']??''),0,150),
                    ];
                }
            }
        }
        $count=0; $uncompressed=0;
        $newMediaIds=[];
        try {
            if($zip->numFiles>50) throw ValidationException::withMessages(['archive'=>'تعداد فایل‌های ZIP بیش از حد مجاز است.']);
            for($i=0;$i<$zip->numFiles;$i++) {
                $stat=$zip->statIndex($i);
                $filename=(string)$stat['name'];
                $ext=strtolower(pathinfo($filename,PATHINFO_EXTENSION));
                if(!in_array($ext,['jpg','jpeg','png','webp'],true))continue;
                $size=(int)$stat['size'];
                if($size>8*1024*1024)continue;
                $uncompressed+=$size;
                if($uncompressed>60*1024*1024 || $count>=20)break;
                $contents=$zip->getFromIndex($i);
                if($contents===false || !@getimagesizefromstring($contents)) continue;
                $temp=tempnam(sys_get_temp_dir(),'talarto-image-');
                try {
                    file_put_contents($temp,$contents);
                    $mime=mime_content_type($temp);
                    if(!in_array($mime,['image/png','image/jpeg','image/webp'],true))continue;
                    $safeName=\Illuminate\Support\Str::uuid().'.'.$ext;
                    $path=Storage::disk('public')->putFileAs(
                       'uploads/gallery',
                       new \Illuminate\Http\File($temp),
                       $safeName
                    );
                    $url=Storage::url($path);
                    $metadata=$manifest[basename($filename)]??[];
                    $newMedia=$this->register($url,$metadata['category']??$category,
                        ($metadata['title']??null) ?: pathinfo(basename($filename),PATHINFO_FILENAME),
                        'concept_visual',strlen($contents),@getimagesize($temp) ?: null,true);
                    $newMediaIds[]=$newMedia->id;
                    $count++;
                } finally { @unlink($temp); }
            }
        } finally { $zip->close(); }
        if($count>0 && $request->boolean('replace_demo')) {
            DB::transaction(function() use($newMediaIds) {
                $old=MediaAsset::where('source','concept_visual')
                    ->where('url','like','/assets/venue/%')
                    ->whereNotIn('id',$newMediaIds)->get();
                foreach($old as $asset) {
                    $asset->update(['active'=>false]);
                    $asset->galleryItem?->update(['active'=>false]);
                }
                foreach($newMediaIds as $index=>$id) {
                    $asset=MediaAsset::findOrFail($id);
                    $asset->update(['sort_order'=>$index]);
                    $asset->galleryItem?->update(['sort_order'=>$index]);
                }
                $first=MediaAsset::findOrFail($newMediaIds[0]);
                $about=MediaAsset::findOrFail($newMediaIds[min(1,count($newMediaIds)-1)]);
                SiteSetting::firstOrCreate([])->update([
                    'hero_image'=>$first->url,'about_image'=>$about->url,
                ]);
            });
        }
        return back()->with('success',"{$count} تصویر از بسته وارد مدیا لایبرری شد.")->withFragment('admin-media');
    }

    private function register(string $url,string $category,string $title,string $source,int $bytes,?array $dim,bool $active): MediaAsset {
        return DB::transaction(function() use($url,$category,$title,$source,$bytes,$dim,$active) {
            $max=(int)MediaAsset::max('sort_order');
            $item=GalleryItem::create([
                'title'=>$title ?: 'تصویر مراسم',
                'category'=>$category,'image'=>$url,'active'=>$active,'sort_order'=>$max+1,
            ]);
            return MediaAsset::create([
                'gallery_item_id'=>$item->id,'title'=>$item->title,'alt_text'=>$item->title,
                'category'=>$category,'url'=>$url,'source'=>$source,'bytes'=>$bytes,
                'width'=>$dim?$dim[0]:null,'height'=>$dim?$dim[1]:null,
                'sort_order'=>$item->sort_order,'active'=>$active,
            ]);
        });
    }

    public function update(Request $request,MediaAsset $mediaAsset) {
        $data=$request->validate([
            'title'=>'required|string|max:150','alt_text'=>'nullable|string|max:220',
            'category'=>'required|string|max:80','sort_order'=>'required|integer|min:0|max:999999',
        ]);
        if(!in_array($data['category'],self::CATEGORIES,true)) {
            throw ValidationException::withMessages(['category'=>'دسته‌بندی تصویر معتبر نیست.']);
        }
        DB::transaction(function() use($request,$data,$mediaAsset) {
            $mediaAsset->update($data+['active'=>$request->boolean('active')]);
            $mediaAsset->galleryItem?->update([
                'title'=>$data['title'],'category'=>$data['category'],
                'sort_order'=>$data['sort_order'],'active'=>$request->boolean('active'),
            ]);
        });
        return back()->with('success','اطلاعات عکس ذخیره شد.')->withFragment('admin-media');
    }

    public function setHero(MediaAsset $mediaAsset) {
        if(!$mediaAsset->active)throw ValidationException::withMessages(['hero'=>'تصویر غیر‌فعال را نمی‌توان برای بنر انتخاب کرد.']);
        SiteSetting::firstOrCreate([])->update(['hero_image'=>$mediaAsset->url]);
        return back()->with('success','تصویر بنر اصلی تغییر کرد.')->withFragment('admin-media');
    }
    public function setAbout(MediaAsset $mediaAsset) {
        if(!$mediaAsset->active)throw ValidationException::withMessages(['about'=>'تصویر غیرفعال قابل انتخاب نیست.']);
        SiteSetting::firstOrCreate([])->update(['about_image'=>$mediaAsset->url]);
        return back()->with('success','تصویر بخش معرفی تغییر کرد.')->withFragment('admin-media');
    }
    public function destroy(MediaAsset $mediaAsset) {
        $url=$mediaAsset->url;
        $isUsed=SiteSetting::query()->where('hero_image',$url)->orWhere('about_image',$url)->exists();
        if($isUsed)throw ValidationException::withMessages(['media'=>'ابتدا عکس دیگری را برای بنر یا درباره ما انتخاب کنید.']);
        DB::transaction(function() use($mediaAsset) {
            $mediaAsset->galleryItem?->delete();
            $mediaAsset->delete();
        });
        // Only remove files previously uploaded under managed storage.
        UploadImage::delete($url);
        return back()->with('success','تصویر از گالری حذف شد.')->withFragment('admin-media');
    }
}
