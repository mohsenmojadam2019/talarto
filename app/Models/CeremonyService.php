<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CeremonyService extends Model{protected $fillable=['title','slug','short_description','description','image','icon','sort_order','active']; protected function casts():array{return ['active'=>'boolean'];} public function getRouteKeyName():string{return 'slug';}}
