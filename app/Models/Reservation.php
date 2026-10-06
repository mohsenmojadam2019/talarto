<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Reservation extends Model {
 protected $fillable=['user_id','tracking_code','name','mobile','event_type','guest_count','child_count','event_date','date_jalali','time_slot','package_id','budget','message','status','admin_note','base_price','guest_total','menu_total','addon_total','date_adjustment_total','discount_total','final_price','paid_amount','payment_status','price_snapshot','hold_expires_at','confirmed_at'];
 protected function casts():array{return ['event_date'=>'date','price_snapshot'=>'array','hold_expires_at'=>'datetime','confirmed_at'=>'datetime'];}
 public function user(){return $this->belongsTo(User::class);}
 public function package(){return $this->belongsTo(Package::class);}
 public function items(){return $this->hasMany(ReservationItem::class);}
 public function quotes(){return $this->hasMany(ReservationQuote::class);}
 public function payments(){return $this->hasMany(Payment::class);}
 public function latestQuote(){return $this->hasOne(ReservationQuote::class)->latestOfMany('version');}
 public function scopeForUser($q,int $userId){return $q->where('user_id',$userId);}
 public function getBalanceAttribute():int{return max(0,(int)$this->final_price-(int)$this->paid_amount);}
}
