<?php
namespace App\Http\Controllers;
use App\Services\PriceCalculator;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
class PricePreviewController extends Controller {
 public function __invoke(Request $r,PriceCalculator $calculator){
  $r->merge(['event_date_jalali'=>JalaliDate::normalize($r->input('event_date_jalali'))]);$d=$r->validate(['guest_count'=>'required|integer|min:10|max:5000','package_id'=>'required|exists:packages,id','event_date_jalali'=>'required|string','event_type'=>'required|string|max:60','time_slot'=>'required|in:day,night','menu_items'=>'nullable|array','menu_items.*'=>'integer','addons'=>'nullable|array']);
  try{$date=JalaliDate::toCarbon($d['event_date_jalali']);}catch(\Throwable){return response()->json(['message'=>'تاریخ معتبر نیست.'],422);}$d['event_date']=$date;return response()->json($calculator->calculate($d));
 }
}
