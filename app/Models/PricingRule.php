<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PricingRule extends Model {
 protected $fillable=['title','direction','rule_type','amount','start_date','end_date','weekdays','package_id','event_type','time_slot','active','priority'];
 protected function casts():array{return ['start_date'=>'date','end_date'=>'date','weekdays'=>'array','active'=>'boolean'];}
 public function package(){return $this->belongsTo(Package::class);}
}
