<?php

namespace Tests\Feature;

use App\Models\{AdminUser, CeremonyService, GalleryItem, MediaAsset, Package, Post, SiteSetting};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class AdminRegressionFixesTest extends TestCase
{
    private function admin(): void
    {
        $user = AdminUser::create([
            'name' => 'Admin QA', 'email' => 'admin-regression@example.test',
            'password' => 'test-not-for-production', 'active' => true,
        ]);
        $this->withSession(['admin_user_id' => $user->id]);
    }

    public function test_optional_fields_can_be_omitted_for_service_package_and_post(): void
    {
        $this->admin();
        $this->post(route('admin.services.store'), ['title' => 'خدمات جدید'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->post(route('admin.packages.store'), ['title' => 'پکیج جدید'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->post(route('admin.posts.store'), ['title' => 'مقاله جدید', 'body' => 'توضیحات مقاله'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertTrue(CeremonyService::where('title', 'خدمات جدید')->exists());
        $this->assertTrue(Package::where('title', 'پکیج جدید')->exists());
        $this->assertTrue(Post::where('title', 'مقاله جدید')->exists());
    }

    public function test_existing_slugs_are_preserved_and_duplicate_slug_is_rejected(): void
    {
        $this->admin();
        $service = CeremonyService::create(['title'=>'خدمات اصلی', 'slug'=>'original-unique-service','active'=>true]);
        $this->put(route('admin.services.update', $service), ['title'=>'خدمات اصلاح‌شده', 'slug'=>''])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('original-unique-service', $service->fresh()->slug);

        $post = Post::create(['title'=>'مقاله اصلی','slug'=>'original-post','body'=>'متن']);
        $this->put(route('admin.posts.update', $post), ['title'=>'مقاله اصلاح‌شده','slug'=>'','body'=>'متن'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('original-post', $post->fresh()->slug);

        $package = Package::create(['title'=>'پکیج اصلی','slug'=>'original-package']);
        $this->put(route('admin.packages.update', $package), ['title'=>'پکیج اصلاح‌شده','slug'=>''])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('original-package', $package->fresh()->slug);

        $this->post(route('admin.services.store'), ['title'=>'نام تکراری','slug'=>'original-unique-service'])
            ->assertSessionHasErrors('slug');
    }

    public function test_site_settings_validate_capacity_and_handle_missing_image_urls(): void
    {
        $this->admin();
        $this->put(route('admin.settings'), ['site_name'=>'تالار تست','hero_title'=>'خوش آمدید'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->put(route('admin.settings'), [
            'site_name'=>'تالار تست','hero_title'=>'خوش آمدید',
            'capacity_min'=>500,'capacity_max'=>50,
        ])->assertSessionHasErrors('capacity_max');
        $this->assertNotEquals(500, SiteSetting::first()->capacity_min);
    }

    public function test_gallery_creation_and_deletion_keep_media_in_sync(): void
    {
        $this->admin();
        $this->post(route('admin.gallery.store'), [
            'title'=>'گالری آزمایشی','category'=>'عقد',
            'image_url'=>'https://example.com/wedding-gallery.jpg','active'=>'1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $gallery = GalleryItem::where('title','گالری آزمایشی')->firstOrFail();
        $media = MediaAsset::where('gallery_item_id',$gallery->id)->firstOrFail();

        $this->delete(route('admin.gallery.delete',$gallery))
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertFalse(MediaAsset::whereKey($media->id)->exists());
        $this->assertFalse(GalleryItem::whereKey($gallery->id)->exists());
    }

    public function test_zip_without_manifest_still_imports_photo(): void
    {
        $this->admin();
        Storage::fake('public');
        $file = tempnam(sys_get_temp_dir(), 'talarto-zip-');
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($file, ZipArchive::OVERWRITE) === true);
        $zip->addFile(public_path('assets/venue/favicon.png'), 'photo.png');
        $zip->close();
        try {
            $this->post(route('admin.media.import'), [
                'archive' => new UploadedFile($file, 'gallery.zip', 'application/zip', null, true),
                'category' => 'عقد',
            ])->assertRedirect()->assertSessionHasNoErrors();
            $this->assertTrue(MediaAsset::where('title','photo')->exists());
        } finally {
            @unlink($file);
        }
    }

    public function test_admin_finance_contains_search_filters_and_pagination_markup(): void
    {
        $this->admin();
        $this->get(route('admin.commerce'))->assertOk()
            ->assertSee('name="q"',false)
            ->assertSee('name="status"',false);
        $this->get(route('admin.dashboard'))->assertOk()
            ->assertSee('name="rq"',false);
    }
}
