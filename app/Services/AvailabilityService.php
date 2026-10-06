<?php
namespace App\Services;
use App\Models\{CalendarDate,CalendarHold,Reservation};
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class AvailabilityService {
 public function cleanupExpired():void{CalendarHold::where('expires_at','<=',now())->delete();}
 public function available(CarbonInterface $date,string $slot,?int $ignoreReservationId=null):bool{
  $this->cleanupExpired();
  if(CalendarDate::whereDate('date',$date)->whereIn('status',['booked','unavailable'])->exists())return false;
  $q=Reservation::whereDate('event_date',$date)->where('time_slot',$slot)->where('status','confirmed');if($ignoreReservationId)$q->whereKeyNot($ignoreReservationId);if($q->exists())return false;
  $h=CalendarHold::whereDate('date',$date)->where('time_slot',$slot)->where('expires_at','>',now());if($ignoreReservationId)$h->where(fn($q)=>$q->whereNull('reservation_id')->orWhere('reservation_id','!=',$ignoreReservationId));return !$h->exists();
 }
 public function placeHold(int $userId,int $reservationId,CarbonInterface $date,string $slot,int $minutes=15):void{
  DB::transaction(function()use($userId,$reservationId,$date,$slot,$minutes){
   $this->cleanupExpired();
   if(!$this->available($date,$slot,$reservationId))throw ValidationException::withMessages(['event_date_jalali'=>'این تاریخ و سانس هم‌اکنون در دسترس نیست.']);
   CalendarHold::where('reservation_id',$reservationId)->delete();
   try{CalendarHold::create(['user_id'=>$userId,'reservation_id'=>$reservationId,'date'=>$date->toDateString(),'time_slot'=>$slot,'expires_at'=>now()->addMinutes($minutes)]);}catch(\Throwable){throw ValidationException::withMessages(['event_date_jalali'=>'این تاریخ همین حالا توسط کاربر دیگری در حال رزرو است.']);}
  });
 }
 public function release(?int $reservationId):void{if($reservationId)CalendarHold::where('reservation_id',$reservationId)->delete();}
}
