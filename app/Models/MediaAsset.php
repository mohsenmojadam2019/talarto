<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model {
    protected $fillable = [
        'gallery_item_id','title','alt_text','category','url','source',
        'bytes','width','height','sort_order','active'
    ];
    protected function casts(): array {
        return ['active'=>'boolean', 'bytes'=>'integer', 'sort_order'=>'integer'];
    }
    public function galleryItem() { return $this->belongsTo(GalleryItem::class); }
}
