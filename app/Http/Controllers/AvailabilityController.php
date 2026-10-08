<?php
namespace App\Http\Controllers;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
class AvailabilityController extends Controller {
 public function __invoke(Request $request,AvailabilityService $availability) {
  $dateStr=JalaliDate::normalize($request->query('date'));
  if(!preg_match('/^1[34]\d{2}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])$/',$dateStr)){
   throw ValidationException::withMessages(['date'=>'تاریخ شمسی معتبر نیست.']);
  }
  try{$date=JalaliDate::toCarbon($dateStr);}
  catch(\Throwable){throw ValidationException::withMessages(['date'=>'تاریخ شمسی معتبر نیست.']);}
  if($date->isBefore(today()))return response()->json(['day'=>false,'night'=>false]);
  $ignoreId=null;
  if($request->user()&&$request->filled('reservation_id')){
   $reservation=Reservation::whereKey($request->integer('reservation_id'))->where('user_id',$request->user()->id)->first();
   if($reservation&&in_array($reservation->status,['new','contacted'],true))$ignoreId=$reservation->id;
  }
  return response()->json([
   'date'=>$dateStr,
   'day'=>$availability->available($date,'day',$ignoreId),
   'night'=>$availability->available($date,'night',$ignoreId),
  ])->header('Cache-Control','no-store, private');
 }
}
