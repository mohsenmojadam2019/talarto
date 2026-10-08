<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CalendarOccupancy extends Model {
    protected $fillable = ['reservation_id', 'date', 'time_slot'];
    public function reservation() { return $this->belongsTo(Reservation::class); }
}
