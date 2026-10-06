<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ActivityLog extends Model {
 protected $fillable=['reservation_id','user_id','actor_type','action','payload'];
 protected function casts():array{return ['payload'=>'array'];}
}
