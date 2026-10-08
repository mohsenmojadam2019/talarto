<?php

namespace Tests\Feature;

use App\Models\{Package,Reservation,User};
use App\Support\JalaliDate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerExperienceTest extends TestCase
{
    private function user(int $i=1): User
    {
        return User::create([
            'name' => 'کاربر آزمایشی',
            'mobile' => sprintf('0912700%04d', $i),
            'password' => 'safe-test-password',
        ]);
    }

    private function reservation(User $user, string $status='confirmed'): Reservation
    {
        $package=Package::firstOrCreate(['slug'=>'qa-luxury'], [
            'title'=>'مراسم آزمایشی','base_price'=>2500000,'per_guest_price'=>0,'min_guests'=>10,'active'=>true,
        ]);
        $eventDate=now()->addDays(45)->toDateString();
        return Reservation::create([
            'user_id'=>$user->id,'name'=>$user->name,'mobile'=>$user->mobile,
            'event_type'=>'عروسی','guest_count'=>160,'event_date'=>$eventDate,
            'date_jalali'=>JalaliDate::format($eventDate),
            'time_slot'=>'night','package_id'=>$package->id,
            'status'=>$status,'final_price'=>2500000,'paid_amount'=>500000,
            'tracking_code'=>'CX-'.strtoupper(bin2hex(random_bytes(4))),
        ]);
    }

    public function test_login_and_register_use_new_light_design_without_dark_promo(): void
    {
        $this->get(route('login'))->assertOk()
            ->assertSee('mw-auth-page')->assertSee('مراسمت را از یک پنل')
            ->assertSee('name="mobile"',false)->assertSee('name="password"',false)
            ->assertDontSee('class="auth-copy"',false);
        $this->get(route('register'))->assertOk()
            ->assertSee('mw-register-form')->assertSee('name="password_confirmation"',false);
    }

    public function test_dashboard_is_private_and_shows_only_own_reservations(): void
    {
        $this->get(route('account.dashboard'))->assertRedirect(route('login'));
        $owner=$this->user();
        $other=$this->user(2);
        $mine=$this->reservation($owner);
        $someoneElses=$this->reservation($other);
        $page=$this->actingAs($owner)->get(route('account.dashboard'));
        $page->assertOk()->assertSee('mw-customer-workspace')
            ->assertSee($mine->tracking_code)->assertDontSee($someoneElses->tracking_code)
            ->assertSee('تالار رویای ماندگار')
            ->assertSee('رزروهای پیش رو')->assertSee('مشاهده جزئیات');
    }

    public function test_profile_requires_authentication(): void
    {
        $this->get(route('account.profile'))->assertRedirect(route('login'));
        $this->patch(route('account.profile.update'), ['name'=>'جعلی'])->assertRedirect(route('login'));
    }

    public function test_customer_can_update_name_and_email_but_not_login_mobile(): void
    {
        $owner=$this->user();
        $this->actingAs($owner)->patch(route('account.profile.update'), [
            'name'=>'نام جدید','email'=>'customer@example.test',
            'mobile'=>'09129999999',
        ])->assertRedirect(route('account.profile'));
        $this->assertSame('نام جدید',$owner->fresh()->name);
        $this->assertSame('customer@example.test',$owner->fresh()->email);
        $this->assertSame('09127000001',$owner->fresh()->mobile);
    }

    public function test_wrong_current_password_cannot_change_password(): void
    {
        $owner=$this->user();
        $this->actingAs($owner)->put(route('account.profile.password'), [
            'current_password'=>'not-the-current-password',
            'password'=>'new-secure-password',
            'password_confirmation'=>'new-secure-password',
        ])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('safe-test-password',$owner->fresh()->password));
    }

    public function test_customer_can_rotate_password_after_verification(): void
    {
        $owner=$this->user();
        $this->actingAs($owner)->put(route('account.profile.password'), [
            'current_password'=>'safe-test-password',
            'password'=>'new-secure-password',
            'password_confirmation'=>'new-secure-password',
        ])->assertRedirect(route('account.profile'))->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-secure-password',$owner->fresh()->password));
        $this->assertFalse(Hash::check('safe-test-password',$owner->fresh()->password));
    }
}
