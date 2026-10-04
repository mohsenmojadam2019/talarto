<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Package extends Model{protected $fillable=['title','slug','subtitle','base_price','per_guest_price','min_guests','description','features','featured','active','sort_order']; protected function casts():array{return ['features'=>'array','featured'=>'boolean','active'=>'boolean'];}}
