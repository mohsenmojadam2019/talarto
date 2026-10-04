<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SiteSetting extends Model{
 protected $fillable=['site_name','phone','whatsapp','instagram','address','map_embed','hero_title','hero_subtitle','hero_image','about_title','about_body','about_image','capacity_min','capacity_max','parking_capacity','amenities','seo_title','seo_description'];
 protected function casts():array{return ['amenities'=>'array'];}
}
