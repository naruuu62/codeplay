<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'full_name',
        'role',
        'avatar_url',
        'is_verified',
        'is_active'
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];
}