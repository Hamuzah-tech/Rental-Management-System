<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
    'name',
    'username',
    'email',
    'phone',
    'second_phone',
    'password',
    'status',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class,'landlord_id');
    }
}