<?php
namespace App\Http\Controllers;

use App\Models\{ActivityLog, Addon, Payment, PricingRule, Reservation, Package};
use App\Services\AvailabilityService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommerceAdminController extends Controller {
 public function index(){
  return view('admin.commerce',[
   'addons'=>Addon::orderBy('category')->orderBy('sort_order')->get(),
   'rules'=>PricingRule::with('package')->orderBy('priority')->get(),
   'reservations'=>Reservation::with(['user','package','latestQuote'])->latest()->paginate(30),
   'payments'=>Payment::with('reservation')->latest()->take(50)->get(),
   'packages'=>Package::where('active',1)->orderBy('sort_order')->get(),
  ]);
 }
 public function exportReservations(): \Symfony\Component\HttpFoundation\StreamedResponse {
  $name='talarto-reservations-'.\Morilog\Jalali\Jalalian::now()->format('Ymd').'.csv';
  return response()->streamDownload(function() {
   $fp=fopen('php://output','w');
   fwrite($fp,"\xEF\xBB\xBF");
   fputcsv($fp,['کد رهگیری','نام مشتری','شماره موبایل','نوع مراسم','تاریخ جلالی','سانس','تعداد مهمان','مبلغ کل','مبلغ پرداخت','وضعیت']);
   foreach(Reservation::orderByDesc('id')->limit(5000)->cursor() as $r) {
    $safe=function($text) {
     $text=preg_replace('/[\r\n\t]+/u',' ',(string)$text);
     return preg_match('/^[=+\-@]/u',$text)?"'".$text:$text;
    };
    fputcsv($fp,[
      $safe($r->tracking_code),$safe($r->name),$safe($r->mobile),$safe($r->event_type),
      $r->date_jalali ?: \App\Support\JalaliDate::format($r->event_date),
      $r->time_slot==='day'?'روز':'شب',(int)$r->guest_count,(int)$r->final_price,
      (int)$r->paid_amount,$safe($r->status)
    ]);
   }
   fclose($fp);
  },$name,['Content-Type'=>'text/csv; charset=UTF-8']);
 }
 public function storeAddon(Request $r){$d=$this->addonData($r);Addon::create($d+['active'=>$r->boolean('active')]);return back()->with('success','خدمت جانبی اضافه شد.');}
 public function updateAddon(Request $r,Addon $addon){$d=$this->addonData($r);$addon->update($d+['active'=>$r->boolean('active')]);return back()->with('success','خدمت به‌روزرسانی شد.');}
 private function addonData(Request $r):array {
  return $r->validate(['category'=>'required|string|max:80','title'=>'required|string|max:150','description'=>'nullable|string|max:1000','pricing_type'=>'required|in:fixed,per_guest,per_table,per_hour,quantity,included','unit_price'=>'required|integer|min:0','min_quantity'=>'nullable|numeric|min:0','max_quantity'=>'nullable|numeric|gte:min_quantity','sort_order'=>'nullable|integer|min:0']);
 }
 public function deleteAddon(Addon $addon){$addon->delete();return back()->with('success','خدمت حذف شد.');}
 public function storeRule(Request $r){$d=$this->ruleData($r);PricingRule::create($d+['active'=>$r->boolean('active')]);return back()->with('success','قانون قیمت‌گذاری ثبت شد.');}
 public function updateRule(Request $r,PricingRule $pricingRule){$pricingRule->update($this->ruleData($r)+['active'=>$r->boolean('active')]);return back()->with('success','قانون به‌روزرسانی شد.');}
 public function deleteRule(PricingRule $pricingRule){$pricingRule->delete();return back()->with('success','قانون حذف شد.');}
 public function storePayment(Request $r,Reservation $reservation){
  $d=$r->validate(['amount'=>'required|integer|min:1','method'=>'required|in:cash,card,transfer,manual','reference'=>'nullable|string|min:3|max:120','note'=>'nullable|string|max:500']);
  try {
   DB::transaction(function()use($r,$reservation,$d) {
    $res=Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();
    if(!in_array($res->status,['new','contacted','confirmed'],true))$this->reject('برای رزرو لغوشده یا تمام‌شده نمی‌توان پرداخت ثبت کرد.');
    if(!$res->quotes()->where('status','accepted')->exists())$this->reject('ابتدا مشتری باید پیش‌فاکتور فعلی را تأیید کند.');
    $paid=(int)$res->payments()->where('status','paid')->sum('amount');
    if((int)$d['amount']>(int)$res->final_price-$paid)$this->reject('مبلغ پرداخت از مانده قرارداد بیشتر است.');
    if(!empty($d['reference'])&&Payment::where('method',$d['method'])->where('reference',$d['reference'])->exists())$this->reject('این شماره پیگیری قبلاً ثبت شده است.');
    Payment::create($d+['reservation_id'=>$res->id,'user_id'=>$res->user_id,'status'=>'paid','paid_at'=>now()]);
    $paid=(int)$res->payments()->where('status','paid')->sum('amount');
    $res->update(['paid_amount'=>$paid,'payment_status'=>$paid===(int)$res->final_price?'paid':'partial']);
    ActivityLog::create(['reservation_id'=>$res->id,'actor_type'=>'admin','action'=>'payment_recorded','payload'=>['amount'=>$d['amount'],'method'=>$d['method'],'reference'=>$d['reference']??null]]);
   },3);
  } catch(QueryException $e) { $this->reject('پرداخت تکراری یا نامعتبر است.'); }
  return back()->with('success','پرداخت ثبت شد.');
 }
 public function confirm(Request $r,Reservation $reservation,AvailabilityService $availability){
  $this->confirmable($reservation);
  $availability->confirm($reservation);
  ActivityLog::create(['reservation_id'=>$reservation->id,'actor_type'=>'admin','action'=>'reservation_confirmed']);
  return back()->with('success','رزرو قطعی شد.');
 }
 public function status(Request $r,Reservation $reservation,AvailabilityService $availability){
  $d=$r->validate(['status'=>'required|in:new,contacted,confirmed,cancelled,done','admin_note'=>'nullable|string|max:2000']);
  $current=$reservation->status;
  $next=$d['status'];
  if($next===$current) {
   $reservation->update(['admin_note'=>$d['admin_note']??null]);
   return back()->with('success','یادداشت رزرو ذخیره شد.');
  }
  $allowed=[
   'new'=>['contacted','confirmed','cancelled'],
   'contacted'=>['confirmed','cancelled'],
   'confirmed'=>['done','cancelled'],
   'cancelled'=>[],
   'done'=>[],
  ];
  if(!in_array($next,$allowed[$current]??[],true))$this->reject('تغییر وضعیت انتخاب‌شده مجاز نیست.');
  if($next==='confirmed') {
   $this->confirmable($reservation);
   $availability->confirm($reservation);
   $reservation->update(['admin_note'=>$d['admin_note']??null]);
  } elseif($next==='cancelled') {
   if($reservation->paid_amount>0)$this->reject('ابتدا وضعیت پرداخت‌ها و استرداد وجه را به‌صورت مالی تعیین تکلیف کنید.');
   DB::transaction(function()use($reservation,$d,$availability){
    $reservation->update(['status'=>'cancelled','admin_note'=>$d['admin_note']??null]);
    $availability->release($reservation->id);
   });
  } elseif($next==='done') {
   if($reservation->event_date->isFuture())$this->reject('مراسم آینده را نمی‌توان برگزارشده ثبت کرد.');
   $reservation->update(['status'=>'done','admin_note'=>$d['admin_note']??null]);
  } else {
   $reservation->update(['status'=>$next,'admin_note'=>$d['admin_note']??null]);
  }
  ActivityLog::create(['reservation_id'=>$reservation->id,'actor_type'=>'admin','action'=>'reservation_status_changed','payload'=>['previous'=>$current,'new'=>$next]]);
  return back()->with('success','وضعیت رزرو ذخیره شد.');
 }
 private function confirmable(Reservation $reservation):void {
  if(!in_array($reservation->status,['new','contacted'],true))$this->reject('این رزرو قابل قطعی‌سازی نیست.');
  if(!$reservation->latestQuote || $reservation->latestQuote->status!=='accepted')$this->reject('ابتدا آخرین پیش‌فاکتور باید توسط مشتری تأیید شود.');
  if($reservation->paid_amount<1)$this->reject('قبل از رزرو قطعی، بیعانه باید در حساب مشتری ثبت شده باشد.');
 }
 private function reject(string $message):never {throw ValidationException::withMessages(['reservation'=>$message]);}
 private function ruleData(Request $r):array {
  $r->merge([
   'start_date_jalali'=>($startInput=\App\Support\JalaliDate::normalize($r->input('start_date_jalali'))) !== '' ? $startInput : null,
   'end_date_jalali'=>($endInput=\App\Support\JalaliDate::normalize($r->input('end_date_jalali'))) !== '' ? $endInput : null,
  ]);
  $d=$r->validate([
   'title'=>'required|string|max:150',
   'direction'=>'required|in:increase,decrease','rule_type'=>'required|in:percentage,fixed',
   'amount'=>'required|integer|min:0',
   'start_date_jalali'=>['nullable','regex:/^1[34]\d{2}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/'],
   'end_date_jalali'=>['nullable','regex:/^1[34]\d{2}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/'],
   'weekdays'=>'nullable|array','weekdays.*'=>'integer|min:0|max:6',
   'package_id'=>'nullable|exists:packages,id','event_type'=>'nullable|string|max:80',
   'time_slot'=>'nullable|in:day,night','priority'=>'nullable|integer|min:0|max:9999'
  ]);
  if($d['rule_type']==='percentage' && $d['amount']>100)$this->reject('درصد قیمت‌گذاری باید بین صفر تا صد باشد.');
  try {
   $start=empty($d['start_date_jalali'])?null:\App\Support\JalaliDate::toCarbon($d['start_date_jalali']);
   $end=empty($d['end_date_jalali'])?null:\App\Support\JalaliDate::toCarbon($d['end_date_jalali']);
  }catch(\Throwable) {
   throw ValidationException::withMessages(['start_date_jalali'=>'تاریخ شمسی معتبر نیست.']);
  }
  if($start && $end && $end->lessThan($start)){
   throw ValidationException::withMessages(['end_date_jalali'=>'پایان بازه باید برابر یا بعد از شروع بازه باشد.']);
  }
  unset($d['start_date_jalali'],$d['end_date_jalali']);
  $d['start_date']=$start?->toDateString();
  $d['end_date']=$end?->toDateString();
  return $d;
 }
}
