<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use Tests\TestCase;

class AdminNeutralThemeTest extends TestCase
{
    private function admin(): AdminUser
    {
        return AdminUser::create([
            'name' => 'مدیر آزمایشی',
            'email' => 'theme-admin@example.test',
            'password' => 'test-only-not-production',
            'active' => true,
        ]);
    }

    public function test_admin_login_uses_white_grey_stylesheet(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('admin-neutral.css', false);
    }

    public function test_admin_and_finance_share_neutral_theme(): void
    {
        $session = ['admin_user_id' => $this->admin()->id];
        $this->withSession($session)->get(route('admin.dashboard'))
            ->assertOk()->assertSee('admin-neutral.css', false)
            ->assertSee('mw-admin-sidebar', false);
        $this->withSession($session)->get(route('admin.commerce'))
            ->assertOk()->assertSee('admin-neutral.css', false)
            ->assertSee('mw-admin-commerce-page', false);
    }

    public function test_public_site_does_not_load_admin_theme(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('admin-neutral.css', false);
    }
}
