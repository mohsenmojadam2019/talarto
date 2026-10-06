<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable {
    protected $fillable=['name','mobile','email','password'];
    protected $hidden=['password','remember_token'];
    protected function casts():array{return ['password'=>'hashed'];}
    public function reservations():HasMany{return $this->hasMany(Reservation::class);}
    public function payments():HasMany{return $this->hasMany(Payment::class);}
}
