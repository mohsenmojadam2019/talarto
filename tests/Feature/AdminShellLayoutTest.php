<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use Tests\TestCase;

class AdminShellLayoutTest extends TestCase
{
    private function admin(): AdminUser
    {
        return AdminUser::create([
            'name' => 'مدیر آزمایشی',
            'email' => 'qa-admin-layout@example.test',
            'password' => 'testing-secure-value',
            'active' => true,
        ]);
    }

    public function test_admin_dashboard_and_commerce_share_same_sidebar(): void
    {
        $admin=$this->admin();
        $dashboard=$this->withSession(['admin_user_id'=>$admin->id])->get(route('admin.dashboard'));
        $commerce=$this->withSession(['admin_user_id'=>$admin->id])->get(route('admin.commerce'));
        $dashboard->assertOk()->assertSee('mw-admin-sidebar',false)->assertSee('mw-admin-main',false);
        $commerce->assertOk()->assertSee('mw-admin-sidebar',false)
            ->assertSee('mw-admin-commerce-page',false)
            ->assertSee('mw-admin-main',false)
            ->assertSee('commerce-grid',false)
            ->assertSee('مدیا لایبرری');
        $commerce->assertDontSee('<section class="section admin-area">',false);
        $commerce->assertDontSee('<section class="admin-head">',false);
    }

    public function test_admin_commerce_has_working_links_to_management_tabs(): void
    {
        $admin=$this->admin();
        $page=$this->withSession(['admin_user_id'=>$admin->id])
            ->get(route('admin.commerce'));
        $page->assertOk()
            ->assertSee(route('admin.dashboard').'#admin-media',false)
            ->assertSee(route('admin.dashboard').'#admin-reservations',false)
            ->assertSee(route('admin.dashboard').'#admin-calendar',false);
    }

    public function test_non_admin_cannot_access_financial_panel(): void
    {
        $this->get(route('admin.commerce'))->assertRedirect(route('admin.login'));
    }
}
