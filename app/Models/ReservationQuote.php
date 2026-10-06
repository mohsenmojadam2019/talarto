<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ReservationQuote extends Model {
 protected $fillable=['reservation_id','version','subtotal','adjustment_total','discount_total','total','status','snapshot','issued_at','accepted_at'];
 protected function casts():array{return ['snapshot'=>'array','issued_at'=>'datetime','accepted_at'=>'datetime'];}
 public function reservation(){return $this->belongsTo(Reservation::class);}
}
