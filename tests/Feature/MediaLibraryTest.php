<?php
namespace Tests\Feature;

use App\Models\{AdminUser,GalleryItem,MediaAsset,SiteSetting};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class MediaLibraryTest extends TestCase {
    private function admin(): array {
        $user=AdminUser::create([
            'name'=>'QA Admin', 'email'=>'qa-media@example.test',
            'password'=>'some-long-test-password', 'active'=>true,
        ]);
        return ['admin_user_id'=>$user->id];
    }
    public function test_guest_cannot_upload_to_media_library(): void {
        $this->post(route('admin.media.store'))->assertRedirect(route('admin.login'));
    }
    public function test_seeded_venue_images_are_indexed_without_deleting_gallery(): void {
        $this->assertGreaterThanOrEqual(10,GalleryItem::count());
        $this->assertGreaterThanOrEqual(10,MediaAsset::count());
        $this->assertDatabaseHas('media_assets', [
            'url'=>'/assets/venue/venue-01.webp','source'=>'concept_visual'
        ]);
    }
    public function test_admin_can_upload_and_manage_image(): void {
        Storage::fake('public');
        $image=new UploadedFile(public_path('assets/venue/favicon.png'),'gallery.png','image/png',null,true);
        $this->withSession($this->admin())->post(route('admin.media.store'),[
            'images'=>[$image], 'category'=>'عقد','title'=>'سفره عقد',
            'is_active'=>'1',
        ])->assertSessionHasNoErrors();
        $media=MediaAsset::where('title','سفره عقد')->firstOrFail();
        $this->assertSame('عقد',$media->category);
        $this->assertTrue($media->galleryItem->active);
        Storage::disk('public')->assertExists(substr($media->url,strlen('/storage/')));
        $this->withSession(['admin_user_id'=>AdminUser::first()->id])->patch(route('admin.media.update',$media),[
            'title'=>'عقد اختصاصی','alt_text'=>'تصویر عقد','category'=>'عقد','sort_order'=>2,
        ])->assertSessionHasNoErrors();
        $this->assertSame('عقد اختصاصی',$media->galleryItem->fresh()->title);
        $this->assertFalse($media->fresh()->active);
    }
    public function test_admin_can_assign_hero_from_gallery(): void {
        $media=MediaAsset::firstOrFail();
        $this->withSession($this->admin())->post(route('admin.media.hero',$media))
            ->assertSessionHasNoErrors();
        $this->assertSame($media->url,SiteSetting::first()->hero_image);
        $this->get('/')->assertOk()->assertSee($media->url);
    }
    public function test_import_archive_preserves_individual_categories(): void {
        if(!class_exists(ZipArchive::class))$this->markTestSkipped('PHP ZIP extension missing');
        Storage::fake('public');
        $zipFile=tempnam(sys_get_temp_dir(),'tlr-zip');
        $archive=new ZipArchive();
        $this->assertTrue($archive->open($zipFile,ZipArchive::OVERWRITE|ZipArchive::CREATE)===true);
        $archive->addFile(public_path('assets/venue/favicon.png'),'venue-aghd.png');
        $archive->addFromString('manifest.json',json_encode(['photos'=>[
            ['file'=>'venue-aghd.png','category'=>'عقد','title'=>'چیدمان سفره عقد'],
        ]],JSON_UNESCAPED_UNICODE));
        $archive->close();
        try{
            $file=new UploadedFile($zipFile,'media.zip','application/zip',null,true);
            $this->withSession($this->admin())->post(route('admin.media.import'),[
                'archive'=>$file,'category'=>'سالن عروسی'
            ])->assertSessionHasNoErrors();
            $this->assertDatabaseHas('media_assets',[
                'title'=>'چیدمان سفره عقد','category'=>'عقد','source'=>'concept_visual'
            ]);
        }finally{ @unlink($zipFile); }
    }
}
