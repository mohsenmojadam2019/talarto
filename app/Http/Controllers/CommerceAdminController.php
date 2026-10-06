<?php
namespace App\Http\Controllers;
use App\Models\{Addon,Payment,PricingRule,Reservation,Package};
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CommerceAdminController extends Controller {
 public function index(){return view('admin.commerce',['addons'=>Addon::orderBy('category')->orderBy('sort_order')->get(),'rules'=>PricingRule::with('package')->orderBy('priority')->get(),'reservations'=>Reservation::with(['user','package','latestQuote'])->latest()->take(80)->get(),'payments'=>Payment::with('reservation')->latest()->take(50)->get(),'packages'=>Package::where('active',1)->orderBy('sort_order')->get()]);}
 public function storeAddon(Request $r){$d=$r->validate(['category'=>'required|string|max:80','title'=>'required|string|max:150','description'=>'nullable|string|max:1000','pricing_type'=>'required|in:fixed,per_guest,per_table,per_hour,quantity,included','unit_price'=>'required|integer|min:0','min_quantity'=>'nullable|numeric|min:0','max_quantity'=>'nullable|numeric|min:0','sort_order'=>'nullable|integer|min:0']);Addon::create($d+['active'=>$r->boolean('active')]);return back()->with('success','خدمت جانبی اضافه شد.');}
 public function updateAddon(Request $r,Addon $addon){$d=$r->validate(['category'=>'required|string|max:80','title'=>'required|string|max:150','description'=>'nullable|string|max:1000','pricing_type'=>'required|in:fixed,per_guest,per_table,per_hour,quantity,included','unit_price'=>'required|integer|min:0','min_quantity'=>'nullable|numeric|min:0','max_quantity'=>'nullable|numeric|min:0','sort_order'=>'nullable|integer|min:0']);$addon->update($d+['active'=>$r->boolean('active')]);return back()->with('success','خدمت به‌روزرسانی شد.');}
 public function deleteAddon(Addon $addon){$addon->delete();return back()->with('success','خدمت حذف شد.');}
 public function storeRule(Request $r){$d=$this->ruleData($r);PricingRule::create($d+['active'=>$r->boolean('active')]);return back()->with('success','قانون قیمت‌گذاری ثبت شد.');}
 public function updateRule(Request $r,PricingRule $pricingRule){$pricingRule->update($this->ruleData($r)+['active'=>$r->boolean('active')]);return back()->with('success','قانون به‌روزرسانی شد.');}
 public function deleteRule(PricingRule $pricingRule){$pricingRule->delete();return back()->with('success','قانون حذف شد.');}
 public function storePayment(Request $r,Reservation $reservation){$d=$r->validate(['amount'=>'required|integer|min:1','method'=>'required|in:cash,card,transfer,gateway,manual','reference'=>'nullable|string|max:120','note'=>'nullable|string|max:500']);DB::transaction(function()use($reservation,$d){Payment::create($d+['reservation_id'=>$reservation->id,'user_id'=>$reservation->user_id,'status'=>'paid','paid_at'=>now()]);$paid=(int)$reservation->payments()->where('status','paid')->sum('amount');$status=$paid<=0?'unpaid':($paid>=$reservation->final_price?'paid':'partial');$reservation->update(['paid_amount'=>$paid,'payment_status'=>$status]);});return back()->with('success','پرداخت ثبت شد.');}
 public function confirm(Request $r,Reservation $reservation,AvailabilityService $availability){if(!$availability->available($reservation->event_date,$reservation->time_slot,$reservation->id))return back()->withErrors(['reservation'=>'تاریخ/سانس دیگر قابل تأیید نیست.']);$reservation->update(['status'=>'confirmed','confirmed_at'=>now(),'hold_expires_at'=>null]);$availability->release($reservation->id);return back()->with('success','رزرو قطعی شد.');}

 public function status(Request $r,Reservation $reservation,AvailabilityService $availability){
  $d=$r->validate(['status'=>'required|in:new,contacted,confirmed,cancelled,done','admin_note'=>'nullable|string|max:2000']);
  if($d['status']==='confirmed'&&!$availability->available($reservation->event_date,$reservation->time_slot,$reservation->id))return back()->withErrors(['reservation'=>'این تاریخ و سانس توسط رزرو دیگری اشغال شده است.']);
  $reservation->update($d+($d['status']==='confirmed'?['confirmed_at'=>now(),'hold_expires_at'=>null]:[]));
  if(in_array($d['status'],['confirmed','cancelled'],true))$availability->release($reservation->id);
  return back()->with('success','وضعیت رزرو ذخیره شد.');
 }
 private function ruleData(Request $r):array{$d=$r->validate(['title'=>'required|string|max:150','direction'=>'required|in:increase,decrease','rule_type'=>'required|in:percentage,fixed','amount'=>'required|integer|min:0','start_date'=>'nullable|date','end_date'=>'nullable|date|after_or_equal:start_date','weekdays'=>'nullable|array','weekdays.*'=>'integer|min:0|max:6','package_id'=>'nullable|exists:packages,id','event_type'=>'nullable|string|max:80','time_slot'=>'nullable|in:day,night','priority'=>'nullable|integer|min:0|max:9999']);return $d;}
}
