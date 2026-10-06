<?php
namespace Tests\Feature;
use App\Models\{Addon,MenuItem,Package,PricingRule,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CustomerReservationTest extends TestCase {
 use RefreshDatabase;
 public function test_customer_can_create_priced_reservation():void{
  PricingRule::query()->delete();
  $user=User::create(['name'=>'کاربر تست','mobile'=>'09120000001','password'=>'secret1']);
  $package=Package::create(['title'=>'تست','slug'=>'test-package','base_price'=>1000000,'per_guest_price'=>100000,'min_guests'=>50,'active'=>1]);
  $menu=MenuItem::create(['category'=>'غذا','title'=>'منو تست','price_per_guest'=>20000,'active'=>1]);
  $addon=Addon::create(['category'=>'دکور','title'=>'دکور تست','pricing_type'=>'fixed','unit_price'=>500000,'active'=>1]);
  $res=$this->actingAs($user)->post(route('account.reservations.store'),['event_type'=>'عروسی','guest_count'=>100,'child_count'=>0,'event_date_jalali'=>'1406/02/25','time_slot'=>'night','package_id'=>$package->id,'menu_items'=>[$menu->id],'addons'=>[$addon->id=>1]]);
  $res->assertRedirect();$this->assertDatabaseCount('reservations',1);$reservation=\App\Models\Reservation::first();$this->assertEquals(13500000,$reservation->final_price);$this->assertGreaterThan(0,$reservation->items()->count());$this->assertEquals(1,$reservation->quotes()->count());
 }
 public function test_weekday_rule_is_applied_server_side():void{
  PricingRule::query()->delete();
  $user=User::create(['name'=>'کاربر تست','mobile'=>'09120000002','password'=>'secret1']);
  $package=Package::create(['title'=>'تست ۲','slug'=>'test-package-2','base_price'=>1000000,'per_guest_price'=>0,'min_guests'=>1,'active'=>1]);
  PricingRule::create(['title'=>'همه روزها','direction'=>'increase','rule_type'=>'percentage','amount'=>10,'active'=>1,'priority'=>1]);
  $preview=$this->actingAs($user)->postJson(route('account.price-preview'),['event_type'=>'عروسی','guest_count'=>100,'event_date_jalali'=>'1406/02/25','time_slot'=>'night','package_id'=>$package->id]);
  $preview->assertOk()->assertJsonPath('final_price',1100000);
 }
}
