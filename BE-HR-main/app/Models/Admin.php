<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;
    protected $fillable = ['username', 'password', 'role', 'remember_token'];
    protected $hidden = ['password', 'remember_token'];
}
