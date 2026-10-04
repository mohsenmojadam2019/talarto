<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Post extends Model{protected $fillable=['title','slug','excerpt','body','cover_image','meta_title','meta_description','published_at']; protected function casts():array{return ['published_at'=>'datetime'];} public function getRouteKeyName():string{return 'slug';}}
