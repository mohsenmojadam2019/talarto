<?php
namespace Tests\Feature;

use App\Models\{AdminUser,CalendarOccupancy,Package,Payment,Reservation,ReservationQuote,SiteSetting,User};
use Tests\TestCase;

class BookingHardeningTest extends TestCase
{
 private function person(int $n=1): User {
  return User::create(['name'=>'Customer','mobile'=>sprintf('0912000%04d',$n),'password'=>'testing-password']);
 }
 private function pack(string $slug='essential'): Package {
  return Package::create(['title'=>'Package','slug'=>$slug,'base_price'=>1000000,'per_guest_price'=>0,'min_guests'=>10,'active'=>true]);
 }
 private function reservation(User $user,Package $pack): Reservation {
  return Reservation::create(['user_id'=>$user->id,'name'=>$user->name,'mobile'=>$user->mobile,
   'event_type'=>'عروسی','guest_count'=>100,'event_date'=>'2027-05-15',
   'date_jalali'=>'1406/02/25','time_slot'=>'night','package_id'=>$pack->id,
   'status'=>'new','final_price'=>1000000,'tracking_code'=>'TLR-'.strtoupper(bin2hex(random_bytes(4)))]);
 }
 private function accepted(Reservation $r): void {
  ReservationQuote::create(['reservation_id'=>$r->id,'version'=>1,'subtotal'=>1000000,
   'total'=>1000000,'status'=>'accepted','accepted_at'=>now(),'issued_at'=>now()]);
 }
 private function admin(): array {
  $a=AdminUser::create(['name'=>'Admin','email'=>'admin@example.test','password'=>'strongpassword123','active'=>true]);
  return ['admin_user_id'=>$a->id];
 }
 public function test_admin_requires_real_account_and_not_legacy_session(): void {
  $this->withSession(['admin_authenticated'=>true])->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
 }
 public function test_admin_can_login_with_hashed_password(): void {
  $this->admin();
  $this->post(route('admin.authenticate'),['email'=>'admin@example.test','password'=>'strongpassword123'])
    ->assertRedirect(route('admin.dashboard'));
  $this->assertNotNull(session('admin_user_id'));
 }
 public function test_confirm_requires_accepted_quote_and_deposit(): void {
  $admin=$this->admin();$r=$this->reservation($this->person(),$this->pack());
  $this->withSession($admin)->post(route('admin.commerce.reservations.confirm',$r))
    ->assertSessionHasErrors('reservation');
  $this->assertSame('new',$r->fresh()->status);
  $this->accepted($r);
  $this->withSession($admin)->post(route('admin.commerce.reservations.confirm',$r))
    ->assertSessionHasErrors('reservation');
 }
 public function test_paid_accepted_quote_confirms_and_occupies_slot(): void {
  $admin=$this->admin();$r=$this->reservation($this->person(),$this->pack());$this->accepted($r);
  $this->withSession($admin)->post(route('admin.commerce.payments.store',$r),[
   'amount'=>250000,'method'=>'card','reference'=>'INV-000001'])->assertSessionHasNoErrors();
  $this->withSession($admin)->post(route('admin.commerce.reservations.confirm',$r))->assertSessionHasNoErrors();
  $this->assertSame('confirmed',$r->fresh()->status);
  $this->assertDatabaseHas('calendar_occupancies',['reservation_id'=>$r->id,'date'=>'2027-05-15','time_slot'=>'night']);
  $this->assertSame(250000,(int)$r->fresh()->paid_amount);
 }
 public function test_second_confirmation_for_same_date_slot_is_rejected(): void {
  $admin=$this->admin();$pack=$this->pack();
  $first=$this->reservation($this->person(1),$pack);$this->accepted($first);
  $this->withSession($admin)->post(route('admin.commerce.payments.store',$first),[
   'amount'=>100000,'method'=>'card','reference'=>'INV-000002']);
  $this->withSession($admin)->post(route('admin.commerce.reservations.confirm',$first))->assertSessionHasNoErrors();
  $second=$this->reservation($this->person(2),$pack);$this->accepted($second);
  $this->withSession($admin)->post(route('admin.commerce.payments.store',$second),[
   'amount'=>100000,'method'=>'card','reference'=>'INV-000003']);
  $this->withSession($admin)->post(route('admin.commerce.reservations.confirm',$second))->assertSessionHasErrors();
  $this->assertSame('new',$second->fresh()->status);
  $this->assertSame(1,CalendarOccupancy::count());
 }
 public function test_customer_cannot_cancel_confirmed_reservation_by_direct_request(): void {
  $u=$this->person();$r=$this->reservation($u,$this->pack());
  $r->update(['status'=>'confirmed']);
  $this->actingAs($u)->post(route('account.reservations.cancel',$r))->assertStatus(422);
  $this->assertSame('confirmed',$r->fresh()->status);
 }
 public function test_payment_is_rejected_when_exceeding_balance():void {
  $admin=$this->admin();$r=$this->reservation($this->person(),$this->pack());$this->accepted($r);
  $this->withSession($admin)->post(route('admin.commerce.payments.store',$r),[
   'amount'=>1000001,'method'=>'cash'])->assertSessionHasErrors('reservation');
  $this->assertSame(0,Payment::count());
 }
 public function test_duplicate_reference_is_rejected():void {
  $admin=$this->admin();$pack=$this->pack();
  $first=$this->reservation($this->person(1),$pack);$this->accepted($first);
  $second=$this->reservation($this->person(2),$pack);$this->accepted($second);
  $this->withSession($admin)->post(route('admin.commerce.payments.store',$first),[
   'amount'=>1000,'method'=>'transfer','reference'=>'BANK-0001'])->assertSessionHasNoErrors();
  $this->withSession($admin)->post(route('admin.commerce.payments.store',$second),[
   'amount'=>1000,'method'=>'transfer','reference'=>'BANK-0001'])->assertSessionHasErrors('reservation');
  $this->assertSame(1,Payment::count());
 }
 public function test_public_price_preview_uses_same_server_engine_as_account():void {
  $u=$this->person();$pack=$this->pack();
  $data=['event_date_jalali'=>'1406/02/25','guest_count'=>100,'event_type'=>'عروسی','time_slot'=>'night','package_id'=>$pack->id];
  $first=$this->postJson(route('public.price-preview'),$data)->assertOk()->json('final_price');
  $second=$this->actingAs($u)->postJson(route('account.price-preview'),$data)->assertOk()->json('final_price');
  $this->assertSame($first,$second);
 }
 public function test_guest_count_cannot_exceed_venue_capacity(): void {
  SiteSetting::create(['site_name'=>'Talarto','capacity_max'=>80]);
  $pack=$this->pack();
  $this->postJson(route('public.price-preview'),[
   'event_date_jalali'=>'1406/02/25','guest_count'=>100,'event_type'=>'عروسی',
   'time_slot'=>'night','package_id'=>$pack->id])->assertUnprocessable()->assertJsonValidationErrors('guest_count');
 }
 public function test_pending_hold_cannot_bypass_blocked_date_in_calendar():void {
  $r=$this->reservation($this->person(),$this->pack());$this->accepted($r);
  $r->update(['status'=>'confirmed']);
  CalendarOccupancy::create(['reservation_id'=>$r->id,'date'=>'2027-05-15','time_slot'=>'night']);
  $this->getJson(route('availability.check',['date'=>'1406/02/25']))->assertOk()->assertJsonPath('night',false)->assertJsonPath('day',true);
 }
}
