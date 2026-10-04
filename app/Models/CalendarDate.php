<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CalendarDate extends Model{protected $fillable=['date','status','note']; protected function casts():array{return ['date'=>'date'];}}
