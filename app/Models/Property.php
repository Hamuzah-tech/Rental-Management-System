<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{

    protected $fillable = [
        'landlord_id',
        'name',
        'address',
        'description',
        'registration_token',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    protected static function booted()
    {
        static::creating(function ($property) {
            if (!$property->registration_token) {
                $property->registration_token = Str::random(40);
            }
        });
    }

    /**
     * Get the registration link for this property
     */
    public function getRegistrationLink()
    {
        return route('tenant.registration', ['token' => $this->registration_token]);
    }
}