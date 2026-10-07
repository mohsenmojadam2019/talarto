<?php
namespace Tests\Feature;
use App\Models\{Addon,Package,MenuItem,PricingRule,Reservation,User};
use App\Services\{AvailabilityService,PriceCalculator,ReservationPricingService};
use Illuminate\Support\Carbon;
use Tests\TestCase;
class CustomerReservationTest extends TestCase {
 public function test_server_side_price_engine_calculates_package_menu_addons_and_rules():void{
  PricingRule::query()->delete();
  $package=Package::create(['title'=>'تست','slug'=>'test','base_price'=>1000000,'per_guest_price'=>100000,'min_guests'=>50,'active'=>1]);
  $menu=MenuItem::create(['category'=>'غذا','title'=>'منو','price_per_guest'=>20000,'active'=>1]);
  $fixed=Addon::create(['category'=>'دکور','title'=>'دکور','pricing_type'=>'fixed','unit_price'=>500000,'min_quantity'=>1,'active'=>1]);
  $tables=Addon::create(['category'=>'تشریفات','title'=>'میز','pricing_type'=>'per_table','unit_price'=>200000,'min_quantity'=>1,'active'=>1]);
  PricingRule::create(['title'=>'افزایش تست','direction'=>'increase','rule_type'=>'percentage','amount'=>10,'active'=>1,'priority'=>1]);
  $price=app(PriceCalculator::class)->calculate(['guest_count'=>100,'package_id'=>$package->id,'menu_items'=>[$menu->id],'addons'=>[$fixed->id=>1,$tables->id=>8],'event_date'=>Carbon::parse('2026-10-08'),'event_type'=>'عروسی','time_slot'=>'night']);
  $this->assertSame(1000000,$price['base_price']);
  $this->assertSame(10000000,$price['guest_total']);
  $this->assertSame(2000000,$price['menu_total']);
  $this->assertSame(2100000,$price['addon_total']);
  $this->assertSame(15100000,$price['subtotal']);
  $this->assertSame(1510000,$price['date_adjustment_total']);
  $this->assertSame(16610000,$price['final_price']);
 }
 public function test_repricing_supersedes_previously_accepted_quote():void{
  $package=Package::create(['title'=>'پکیج','slug'=>'quote-test','base_price'=>1000000,'per_guest_price'=>0,'min_guests'=>1,'active'=>1]);
  $reservation=Reservation::create(['name'=>'تست','mobile'=>'09120000000','event_type'=>'عروسی','guest_count'=>100,'event_date'=>'2026-11-10','date_jalali'=>'1405/08/19','time_slot'=>'night','package_id'=>$package->id,'status'=>'new']);
  $selection=['guest_count'=>100,'package_id'=>$package->id,'menu_items'=>[],'addons'=>[],'event_date'=>Carbon::parse('2026-11-10'),'event_type'=>'عروسی','time_slot'=>'night'];
  app(ReservationPricingService::class)->recalculate($reservation,$selection,true);
  $first=$reservation->quotes()->first();$first->update(['status'=>'accepted','accepted_at'=>now()]);
  app(ReservationPricingService::class)->recalculate($reservation,$selection,true);
  $this->assertSame('superseded',$first->fresh()->status);
  $this->assertSame(2,$reservation->quotes()->count());
  $this->assertSame('issued',$reservation->quotes()->orderByDesc('version')->first()->status);
 }
 public function test_calendar_hold_blocks_same_slot_and_releases_cleanly():void{
  $user=User::create(['name'=>'کاربر','mobile'=>'09120000003','password'=>'secret1']);
  $package=Package::create(['title'=>'پکیج','slug'=>'hold-test','base_price'=>0,'per_guest_price'=>0,'min_guests'=>1,'active'=>1]);
  $reservation=Reservation::create(['user_id'=>$user->id,'name'=>$user->name,'mobile'=>$user->mobile,'event_type'=>'عروسی','guest_count'=>100,'event_date'=>'2026-11-11','date_jalali'=>'1405/08/20','time_slot'=>'night','package_id'=>$package->id,'status'=>'new']);
  $date=Carbon::parse('2026-11-11');$availability=app(AvailabilityService::class);
  $availability->placeHold($user->id,$reservation->id,$date,'night');
  $this->assertFalse($availability->available($date,'night'));
  $this->assertTrue($availability->available($date,'day'));
  $availability->release($reservation->id);
  $this->assertTrue($availability->available($date,'night'));
 }
}
