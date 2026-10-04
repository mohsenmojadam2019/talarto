<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes;
class Venue extends Model{use SoftDeletes; protected $fillable=['name','slug','type','city','district','address','capacity_min','capacity_max','price_from','price_to','short_description','description','cover_image','gallery','features','event_types','parking_capacity','is_verified','is_featured','discount_percent','meta_title','meta_description','published_at']; protected function casts():array{return ['gallery'=>'array','features'=>'array','event_types'=>'array','is_verified'=>'boolean','is_featured'=>'boolean','published_at'=>'datetime'];} public function getRouteKeyName():string{return 'slug';} public function bookings(){return $this->hasMany(Booking::class);} }
