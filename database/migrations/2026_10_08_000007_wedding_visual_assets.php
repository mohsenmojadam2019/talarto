<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        $photos=[
          ['تالار اصلی و مراسم عروسی','سالن'],
          ['جایگاه ویژه عروس و داماد','جایگاه'],
          ['ورودی تالار و محوطه','محوطه'],
          ['جشن عقد و نامزدی','عقد'],
          ['دکوراسیون و میز مهمانان','دکوراسیون'],
          ['سرو دسر و شیرینی','پذیرایی'],
          ['باغ و فضای باز','محوطه'],
          ['سوئیت و اتاق عروس','امکانات'],
          ['نورپردازی ورودی','نورپردازی'],
          ['چیدمان سالن پذیرایی','سالن'],
        ];
        foreach($photos as $index=>$info) {
            $asset='/assets/venue/venue-'.str_pad((string)($index+1),2,'0',STR_PAD_LEFT).'.webp';
            $entry=DB::table('gallery_items')->orderBy('sort_order')->skip($index)->first();
            if($entry) {
                // Preserve user-uploaded photographs; replace only old demonstration/placeholder URLs.
                if(str_contains((string)$entry->image,'images.unsplash.com') || str_contains((string)$entry->image,'/assets/venue/')) {
                    DB::table('gallery_items')->where('id',$entry->id)->update([
                      'image'=>$asset, 'title'=>$info[0], 'category'=>$info[1], 'active'=>true, 'sort_order'=>$index, 'updated_at'=>now()
                    ]);
                }
            }else {
                DB::table('gallery_items')->insert([
                  'title'=>$info[0], 'category'=>$info[1], 'image'=>$asset,
                  'active'=>true, 'sort_order'=>$index, 'created_at'=>now(), 'updated_at'=>now(),
                ]);
            }
        }
        foreach(DB::table('ceremony_services')->orderBy('sort_order')->get() as $index=>$service) {
            if(str_contains((string)$service->image,'images.unsplash.com')) {
                DB::table('ceremony_services')->where('id',$service->id)->update([
                    'image'=>'/assets/venue/venue-'.str_pad((string)(($index%10)+1),2,'0',STR_PAD_LEFT).'.webp',
                    'updated_at'=>now(),
                ]);
            }
        }
        DB::table('site_settings')->where('id',1)->update([
          'hero_title'=>'شکوه یک شب عاشقانه',
          'site_name'=>'تالار رویای ماندگار',
          'address'=>'تهران، زعفرانیه (نشانی کامل پس از هماهنگی بازدید)',
          'hero_image'=>'/assets/venue/venue-01.webp',
          'about_image'=>'/assets/venue/venue-02.webp',
          'updated_at'=>now()
        ]);
    }
    public function down(): void {}
};
