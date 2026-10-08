<?php
namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\PriceCalculator;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PricePreviewController extends Controller {
 public function __invoke(Request $r, PriceCalculator $calculator){
  $r->merge(['event_date_jalali'=>JalaliDate::normalize($r->input('event_date_jalali'))]);
  $d=$r->validate([
   'guest_count'=>'required|integer|min:10|max:5000',
   'package_id'=>['required',Rule::exists('packages','id')->where('active',1)],
   'event_date_jalali'=>['required','regex:/^1[34]\d{2}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/'],
   'event_type'=>'required|string|max:60','time_slot'=>'required|in:day,night',
   'menu_items'=>'nullable|array','menu_items.*'=>'integer|distinct|exists:menu_items,id',
   'addons'=>'nullable|array','addons.*'=>'nullable|numeric|min:0|max:999',
  ]);
  try{$date=JalaliDate::toCarbon($d['event_date_jalali']);}
  catch(\Throwable){throw ValidationException::withMessages(['event_date_jalali'=>'تاریخ معتبر نیست.']);}
  if($date->isBefore(today()))throw ValidationException::withMessages(['event_date_jalali'=>'تاریخ مراسم نمی‌تواند گذشته باشد.']);
  $capacity=SiteSetting::value('capacity_max');
  if($capacity && $d['guest_count']>$capacity)throw ValidationException::withMessages(['guest_count'=>'تعداد مهمان از ظرفیت مجموعه بیشتر است.']);
  $d['event_date']=$date;
  return response()->json($calculator->calculate($d));
 }
}
