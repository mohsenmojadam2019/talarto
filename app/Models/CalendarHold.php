<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CalendarHold extends Model {
 protected $fillable=['user_id','reservation_id','date','time_slot','expires_at'];
 protected function casts():array{return ['date'=>'date','expires_at'=>'datetime'];}
}
