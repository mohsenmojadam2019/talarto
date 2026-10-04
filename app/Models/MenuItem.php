<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MenuItem extends Model{protected $fillable=['category','title','description','price_per_guest','active','sort_order']; protected function casts():array{return ['active'=>'boolean'];}}
