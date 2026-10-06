<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ReservationItem extends Model {
 protected $fillable=['reservation_id','item_type','item_id','title_snapshot','pricing_type','quantity','unit_price','total_price','metadata'];
 protected function casts():array{return ['metadata'=>'array','quantity'=>'decimal:2'];}
 public function reservation(){return $this->belongsTo(Reservation::class);}
}
