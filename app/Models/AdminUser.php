<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
class AdminUser extends Authenticatable {
    protected $fillable = ['name', 'email', 'password', 'active'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array { return ['password' => 'hashed', 'active' => 'boolean']; }
}
