<?php

namespace Tests\Feature;

use App\Models\{AdminUser, Package, Reservation};
use App\Support\JalaliDate;
use Morilog\Jalali\Jalalian;
use Tests\TestCase;

class JalaliUiTest extends TestCase {
    private function adminSession(): array {
        $admin=AdminUser::create([
            'name'=>'QA Admin',
            'email'=>'jalali-qa@example.test',
            'password'=>'secure-password-for-test',
            'active'=>true,
        ]);
        return ['admin_user_id'=>$admin->id];
    }
    private function ruleData(array $overrides=[]): array {
        return array_merge([
            'title'=>'قانون پاییز',
            'direction'=>'increase','rule_type'=>'percentage',
            'amount'=>12,'start_date_jalali'=>'۱۴۰۵/۰۷/۱۶',
            'end_date_jalali'=>'۱۴۰۵/۰۸/۲۰',
            'priority'=>100,'active'=>'1',
        ],$overrides);
    }
    public function test_print_helper_formats_jalali_not_gregorian(): void {
        $instant=Jalalian::fromFormat('Y/m/d','1405/07/16')->toCarbon()->setTime(16,45);
        $this->assertSame('1405/07/16',JalaliDate::format($instant));
        $this->assertSame('1405/07/16 16:45',JalaliDate::formatDateTime($instant));
        $this->assertSame('۱۴۰۵/۰۷/۱۶',JalaliDate::display('1405/07/16'));
    }
    public function test_pricing_rule_only_accepts_jalali_date_inputs(): void {
        $this->withSession($this->adminSession())->post(route('admin.commerce.rules.store'),
            $this->ruleData())->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pricing_rules',['title'=>'قانون پاییز']);
        $rule=\App\Models\PricingRule::where('title','قانون پاییز')->firstOrFail();
        $this->assertSame('1405/07/16',JalaliDate::format($rule->start_date));
        $this->assertSame('1405/08/20',JalaliDate::format($rule->end_date));
    }
    public function test_price_rule_allows_open_ended_date_range(): void {
        $this->withSession($this->adminSession())->post(route('admin.commerce.rules.store'),
            $this->ruleData(['start_date_jalali'=>'','end_date_jalali'=>'']))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pricing_rules',['title'=>'قانون پاییز',
            'start_date'=>null,'end_date'=>null]);
    }
    public function test_reversed_jalali_date_range_is_rejected(): void {
        $this->withSession($this->adminSession())->post(route('admin.commerce.rules.store'),
            $this->ruleData(['start_date_jalali'=>'1405/08/20','end_date_jalali'=>'1405/07/16']))
            ->assertSessionHasErrors('end_date_jalali');
    }
    public function test_admin_commerce_has_no_native_gregorian_date_input(): void {
        $this->withSession($this->adminSession())->get(route('admin.commerce'))
            ->assertOk()->assertSee('start_date_jalali')->assertDontSee('type="date"',false);
    }
    public function test_customer_does_not_receive_admin_csv(): void {
        $this->get(route('admin.commerce.reservations.export'))->assertRedirect(route('admin.login'));
    }
    public function test_admin_csv_export_contains_only_jalali_dates(): void {
        $pkg=Package::create(['title'=>'انتخابی','slug'=>'unit-export','base_price'=>1000000,'min_guests'=>10,'active'=>true]);
        Reservation::create([
            'name'=>'مهمان آزمایشی', 'mobile'=>'09123456789',
            'event_type'=>'عروسی','guest_count'=>80,
            'event_date'=>JalaliDate::toCarbon('1405/07/16'),
            'date_jalali'=>'1405/07/16','time_slot'=>'night',
            'package_id'=>$pkg->id,'status'=>'new','final_price'=>1000000,
            'tracking_code'=>'TLR-JALALI-01',
        ]);
        $resp=$this->withSession($this->adminSession())->get(route('admin.commerce.reservations.export'));
        $resp->assertOk();
        $csv=$resp->streamedContent();
        $this->assertStringContainsString('1405/07/16',$csv);
        $this->assertStringNotContainsString('2026-10-08',$csv);
        $this->assertStringContainsString('TLR-JALALI-01',$csv);
    }
}
