<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GalleryItem extends Model{protected $fillable=['title','category','image','video_url','sort_order','active']; protected function casts():array{return ['active'=>'boolean'];}}
