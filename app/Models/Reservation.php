<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Reservation extends Model{protected $fillable=['name','mobile','event_type','guest_count','event_date','date_jalali','package_id','budget','message','status','admin_note']; protected function casts():array{return ['event_date'=>'date'];} public function package(){return $this->belongsTo(Package::class);}}
