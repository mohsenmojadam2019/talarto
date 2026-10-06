<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
 protected $fillable=['reservation_id','user_id','amount','method','status','reference','paid_at','note'];
 protected function casts():array{return ['paid_at'=>'datetime'];}
 public function reservation(){return $this->belongsTo(Reservation::class);}
 public function user(){return $this->belongsTo(User::class);}
}
