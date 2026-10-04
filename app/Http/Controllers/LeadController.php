<?php
namespace App\Http\Controllers;
use App\Models\{Reservation,VisitRequest,ContactMessage,CalendarDate,Testimonial};
use App\Support\{JalaliDate,LeadNotifier};
use Illuminate\Http\Request;
class LeadController extends Controller {
 public function reservation(Request $r){
  $r->merge(['event_date_jalali'=>JalaliDate::normalize($r->input('event_date_jalali'))]);
  $d=$r->validate(['name'=>'required|string|max:100','mobile'=>['required','regex:/^09\d{9}$/'],'event_type'=>'required|string|max:60','guest_count'=>'required|integer|min:10|max:5000','event_date_jalali'=>['required','regex:/^1[34]\d{2}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/'],'package_id'=>'nullable|exists:packages,id','budget'=>'nullable|integer|min:0','message'=>'nullable|string|max:1500']);
  try{$date=JalaliDate::toCarbon($d['event_date_jalali']);}catch(\Throwable){return back()->withErrors(['event_date_jalali'=>'تاریخ شمسی معتبر نیست.'])->withInput();}
  if($date->isBefore(today()))return back()->withErrors(['event_date_jalali'=>'تاریخ مراسم نمی‌تواند گذشته باشد.'])->withInput();
  $taken=CalendarDate::whereDate('date',$date)->whereIn('status',['booked','unavailable'])->exists()||Reservation::whereDate('event_date',$date)->where('status','confirmed')->exists();
  if($taken)return back()->withErrors(['event_date_jalali'=>'این تاریخ در حال حاضر در دسترس نیست.'])->withInput();
  $jalali=$d['event_date_jalali']; unset($d['event_date_jalali']);
  $lead=Reservation::create($d+['event_date'=>$date->toDateString(),'date_jalali'=>$jalali,'status'=>'new']);
  LeadNotifier::send('درخواست رزرو جدید تالارتو',['نام'=>$lead->name,'موبایل'=>$lead->mobile,'مراسم'=>$lead->event_type,'تاریخ'=>$jalali,'مهمان'=>$lead->guest_count]);
  return back()->with('success','درخواست شما ثبت شد. برای هماهنگی نهایی با شما تماس می‌گیریم.');
 }
 public function visit(Request $r){
  $r->merge(['preferred_date_jalali'=>JalaliDate::normalize($r->input('preferred_date_jalali'))]);
  $d=$r->validate(['name'=>'required|string|max:100','mobile'=>['required','regex:/^09\d{9}$/'],'preferred_date_jalali'=>['required','regex:/^1[34]\d{2}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/'],'guest_count'=>'nullable|integer|min:1|max:5000','message'=>'nullable|string|max:1000']);
  try{$date=JalaliDate::toCarbon($d['preferred_date_jalali']);}catch(\Throwable){return back()->withErrors(['preferred_date_jalali'=>'تاریخ شمسی معتبر نیست.'])->withInput();}
  if($date->isBefore(today()))return back()->withErrors(['preferred_date_jalali'=>'تاریخ بازدید نمی‌تواند گذشته باشد.'])->withInput();
  $jalali=$d['preferred_date_jalali']; unset($d['preferred_date_jalali']);
  $lead=VisitRequest::create($d+['preferred_date'=>$date->toDateString(),'date_jalali'=>$jalali,'status'=>'new']);
  LeadNotifier::send('درخواست بازدید جدید تالارتو',['نام'=>$lead->name,'موبایل'=>$lead->mobile,'تاریخ'=>$jalali,'مهمان'=>$lead->guest_count]);
  return back()->with('success','درخواست بازدید ثبت شد.');
 }
 public function contact(Request $r){$d=$r->validate(['name'=>'required|string|max:100','mobile'=>['required','regex:/^09\d{9}$/'],'subject'=>'nullable|string|max:150','message'=>'required|string|max:2000']);$lead=ContactMessage::create($d+['status'=>'new']);LeadNotifier::send('پیام تماس جدید تالارتو',['نام'=>$lead->name,'موبایل'=>$lead->mobile,'موضوع'=>$lead->subject,'پیام'=>$lead->message]);return back()->with('success','پیام شما ثبت شد.');}
 public function testimonial(Request $r){$d=$r->validate(['name'=>'required|string|max:100','event_type'=>'nullable|string|max:80','rating'=>'required|integer|min:1|max:5','body'=>'required|string|min:10|max:1500']);Testimonial::create($d+['status'=>'pending']);return back()->with('success','نظر شما ثبت شد و پس از بررسی نمایش داده می‌شود.');}
}
