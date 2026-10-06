<?php
namespace App\Providers;
use App\Models\{CalendarDate,CalendarHold,Reservation,SiteSetting};
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider {
 public function register():void{}
 public function boot():void {
  try {$settings=SiteSetting::query()->first();} catch (\Throwable) {$settings=null;}
  View::share('siteSettings',$settings);
  Reservation::updated(function(Reservation $reservation){
   if(!$reservation->wasChanged('status'))return;
   if($reservation->status==='confirmed'){
    CalendarHold::where('reservation_id',$reservation->id)->delete();
    if(!$reservation->confirmed_at)$reservation->updateQuietly(['confirmed_at'=>now(),'hold_expires_at'=>null]);
   }
   if($reservation->status==='cancelled'){
    CalendarHold::where('reservation_id',$reservation->id)->delete();
    CalendarDate::whereDate('date',$reservation->event_date)->where('note','رزرو تایید شده #'.$reservation->id)->delete();
    $reservation->updateQuietly(['hold_expires_at'=>null]);
   }
  });
 }
}
