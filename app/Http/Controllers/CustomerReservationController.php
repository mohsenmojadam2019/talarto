<?php
namespace App\Http\Controllers;
use App\Models\{ActivityLog,Addon,MenuItem,Package,Reservation};
use App\Services\{AvailabilityService,ReservationPricingService};
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class CustomerReservationController extends Controller {
 public function __construct(private ReservationPricingService $pricing,private AvailabilityService $availability){}
 public function index(Request $r){return view('account.reservations.index',['reservations'=>$r->user()->reservations()->with(['package','latestQuote'])->latest()->paginate(12)]);}
 public function create(){return view('account.reservations.form',$this->formData(new Reservation()));}
 public function store(Request $r){
  [$d,$date,$selection]=$this->validatedSelection($r);
  if(!$this->availability->available($date,$d['time_slot']))return back()->withErrors(['event_date_jalali'=>'این تاریخ و سانس در دسترس نیست.'])->withInput();
  $reservation=DB::transaction(function()use($r,$d,$date,$selection){
   $res=Reservation::create(['user_id'=>$r->user()->id,'tracking_code'=>$this->trackingCode(),'name'=>$r->user()->name,'mobile'=>$r->user()->mobile,'event_type'=>$d['event_type'],'guest_count'=>$d['guest_count'],'child_count'=>$d['child_count']??0,'event_date'=>$date->toDateString(),'date_jalali'=>$d['event_date_jalali'],'time_slot'=>$d['time_slot'],'package_id'=>$d['package_id']??null,'message'=>$d['message']??null,'status'=>'new','hold_expires_at'=>now()->addMinutes(15)]);
   $selection['event_date']=$date;$this->pricing->recalculate($res,$selection,true);$this->availability->placeHold($r->user()->id,$res->id,$date,$d['time_slot']);ActivityLog::create(['reservation_id'=>$res->id,'user_id'=>$r->user()->id,'actor_type'=>'user','action'=>'reservation_created','payload'=>['tracking_code'=>$res->tracking_code]]);return $res;
  });
  return redirect()->route('account.reservations.show',$reservation)->with('success','درخواست رزرو و پیش‌فاکتور اولیه ثبت شد.');
 }
 public function show(Request $r,Reservation $reservation){$this->own($r,$reservation);$reservation->load(['package','items','quotes'=>fn($q)=>$q->orderByDesc('version'),'payments'=>fn($q)=>$q->latest()]);return view('account.reservations.show',compact('reservation'));}
 public function printQuote(Request $r,Reservation $reservation){$this->own($r,$reservation);$quote=$reservation->latestQuote;abort_unless($quote,404);return view('account.reservations.print',compact('reservation','quote'));}
 public function edit(Request $r,Reservation $reservation){$this->own($r,$reservation);abort_unless(in_array($reservation->status,['new','contacted'],true)&&(int)$reservation->paid_amount===0,403);return view('account.reservations.form',$this->formData($reservation));}
 public function update(Request $r,Reservation $reservation){
  $this->own($r,$reservation);abort_unless(in_array($reservation->status,['new','contacted'],true)&&(int)$reservation->paid_amount===0,403);[$d,$date,$selection]=$this->validatedSelection($r);
  if(!$this->availability->available($date,$d['time_slot'],$reservation->id))return back()->withErrors(['event_date_jalali'=>'این تاریخ و سانس در دسترس نیست.'])->withInput();
  DB::transaction(function()use($r,$reservation,$d,$date,$selection){$reservation->update(['event_type'=>$d['event_type'],'guest_count'=>$d['guest_count'],'child_count'=>$d['child_count']??0,'event_date'=>$date->toDateString(),'date_jalali'=>$d['event_date_jalali'],'time_slot'=>$d['time_slot'],'package_id'=>$d['package_id']??null,'message'=>$d['message']??null,'hold_expires_at'=>now()->addMinutes(15)]);$selection['event_date']=$date;$this->pricing->recalculate($reservation,$selection,true);$this->availability->placeHold($r->user()->id,$reservation->id,$date,$d['time_slot']);ActivityLog::create(['reservation_id'=>$reservation->id,'user_id'=>$r->user()->id,'actor_type'=>'user','action'=>'reservation_updated']);});
  return redirect()->route('account.reservations.show',$reservation)->with('success','انتخاب‌ها و پیش‌فاکتور به‌روزرسانی شد.');
 }
 public function acceptQuote(Request $r,Reservation $reservation){$this->own($r,$reservation);$r->validate(['accept_terms'=>'accepted']);abort_unless(in_array($reservation->status,['new','contacted'],true),422);DB::transaction(function()use($r,$reservation){$res=Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();$quote=$res->quotes()->where('status','issued')->orderByDesc('version')->first();abort_unless($quote,422);$quote->update(['status'=>'accepted','accepted_at'=>now()]);ActivityLog::create(['reservation_id'=>$res->id,'user_id'=>$r->user()->id,'actor_type'=>'user','action'=>'quote_accepted','payload'=>['quote_version'=>$quote->version,'total'=>$quote->total]]);});return back()->with('success','پیش‌فاکتور تأیید شد؛ برای قطعی‌کردن رزرو، بیعانه و تأیید مجموعه لازم است.');}
 public function cancel(Request $r,Reservation $reservation){$this->own($r,$reservation);abort_unless(in_array($reservation->status,['new','contacted'],true)&&(int)$reservation->paid_amount===0,422);DB::transaction(function()use($r,$reservation){$reservation->update(['status'=>'cancelled','hold_expires_at'=>null]);$this->availability->release($reservation->id);ActivityLog::create(['reservation_id'=>$reservation->id,'user_id'=>$r->user()->id,'actor_type'=>'user','action'=>'reservation_cancelled']);});return redirect()->route('account.dashboard')->with('success','درخواست رزرو لغو شد.');}
 private function validatedSelection(Request $r):array{
  $r->merge(['event_date_jalali'=>JalaliDate::normalize($r->input('event_date_jalali'))]);
  $d=$r->validate(['event_type'=>'required|string|max:60','guest_count'=>'required|integer|min:10|max:5000','child_count'=>'nullable|integer|min:0|max:1000','event_date_jalali'=>['required','regex:/^1[34]\d{2}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/'],'time_slot'=>'required|in:day,night','package_id'=>'required|exists:packages,id','menu_items'=>'nullable|array','menu_items.*'=>'integer|exists:menu_items,id','addons'=>'nullable|array','addons.*'=>'nullable|numeric|min:0|max:999','message'=>'nullable|string|max:1500']);
  try{$date=JalaliDate::toCarbon($d['event_date_jalali']);}catch(\Throwable){throw \Illuminate\Validation\ValidationException::withMessages(['event_date_jalali'=>'تاریخ شمسی معتبر نیست.']);}if($date->isBefore(today()))throw \Illuminate\Validation\ValidationException::withMessages(['event_date_jalali'=>'تاریخ مراسم نمی‌تواند گذشته باشد.']);
  if(($d['child_count']??0)>$d['guest_count'])throw \Illuminate\Validation\ValidationException::withMessages(['child_count'=>'تعداد کودکان نمی‌تواند بیشتر از کل مهمانان باشد.']);
  $capacity=(int)(\App\Models\SiteSetting::value('capacity_max')?:5000);if($d['guest_count']>$capacity)throw \Illuminate\Validation\ValidationException::withMessages(['guest_count'=>'تعداد مهمان از ظرفیت مجموعه بیشتر است.']);
  $selection=['guest_count'=>$d['guest_count'],'package_id'=>$d['package_id'],'menu_items'=>$d['menu_items']??[],'addons'=>$d['addons']??[],'event_type'=>$d['event_type'],'time_slot'=>$d['time_slot']];return [$d,$date,$selection];
 }
 private function formData(Reservation $reservation):array{
  $selectedMenu=[];$selectedAddons=[];if($reservation->exists){foreach($reservation->items as $item){if($item->item_type==='menu')$selectedMenu[]=$item->item_id;if($item->item_type==='addon')$selectedAddons[$item->item_id]=(float)($item->metadata['requested_quantity']??$item->quantity);}}
  $blocked=\App\Models\CalendarDate::whereDate('date','>=',today())->whereIn('status',['booked','unavailable'])->pluck('date')->map(fn($d)=>\App\Support\JalaliDate::format($d));
  return ['reservation'=>$reservation,'packages'=>Package::where('active',1)->orderByDesc('featured')->orderBy('sort_order')->get(),'menuItems'=>MenuItem::where('active',1)->orderBy('category')->orderBy('sort_order')->get()->groupBy('category'),'addons'=>Addon::where('active',1)->orderBy('category')->orderBy('sort_order')->get()->groupBy('category'),'selectedMenu'=>$selectedMenu,'selectedAddons'=>$selectedAddons,'blockedDates'=>$blocked->unique()->values()];
 }
 private function own(Request $r,Reservation $reservation):void{abort_unless((int)$reservation->user_id===(int)$r->user()->id,403);}
 private function trackingCode():string{do{$code='TLR-'.strtoupper(Str::random(8));}while(Reservation::where('tracking_code',$code)->exists());return $code;}
}
