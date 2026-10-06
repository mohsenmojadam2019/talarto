<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Addon extends Model {
 protected $fillable=['category','title','description','pricing_type','unit_price','min_quantity','max_quantity','active','sort_order','metadata'];
 protected function casts():array{return ['active'=>'boolean','metadata'=>'array','min_quantity'=>'decimal:2','max_quantity'=>'decimal:2'];}
}
