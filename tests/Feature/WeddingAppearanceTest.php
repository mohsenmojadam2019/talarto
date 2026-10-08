<?php
namespace Tests\Feature;

use App\Models\GalleryItem;
use Tests\TestCase;

class WeddingAppearanceTest extends TestCase
{
    public function test_homepage_is_single_venue_and_contains_no_package_cards(): void {
        $response=$this->get('/');
        $response->assertOk()->assertSee('شکوه یک شب')->assertSee('زعفرانیه')->assertSee('redcoweb.ir');
        $response->assertDontSee('package-grid')->assertDontSee('سه سطح برای سه نوع برنامه');
    }
    public function test_guest_can_access_user_login_and_admin_login(): void {
        $this->get('/login')->assertOk();
        $this->get('/admin/login')->assertOk();
        $this->get('/account')->assertRedirect();
    }
    public function test_wedding_gallery_has_ten_local_visual_slots(): void {
        $this->assertGreaterThanOrEqual(10, GalleryItem::count());
        $this->assertDatabaseHas('gallery_items', ['image'=>'/assets/venue/venue-10.webp']);
    }
}
