<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VisitRequest extends Model{protected $fillable=['name','mobile','preferred_date','date_jalali','guest_count','message','status','admin_note']; protected function casts():array{return ['preferred_date'=>'date'];}}
