<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Booking extends Model{protected $fillable=['venue_id','name','mobile','event_type','guest_count','event_date','budget','message','status']; protected function casts():array{return ['event_date'=>'date'];} public function venue(){return $this->belongsTo(Venue::class);}}
