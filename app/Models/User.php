<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'name',
        'email',
        'phone',
        'second_phone',
        'password',
        'role',
        'status',
        'landlord_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    public function hasRole($role)
    {
        return strtolower($this->role) === strtolower($role);
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\LandlordResetPasswordNotification($token));
    }
}