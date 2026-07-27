<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Landlord extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'business_name',
        'phone',
        'second_phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'tax_id',
        'company_logo',
        'website',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the landlord profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the properties for the landlord.
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Get the tenants for the landlord through properties.
     */
    public function tenants()
    {
        return $this->hasManyThrough(Tenant::class, Property::class);
    }

    /**
     * Get the full name of the landlord.
     */
    public function getNameAttribute()
    {
        return $this->user ? $this->user->name : null;
    }

    /**
     * Get the email of the landlord.
     */
    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : null;
    }

    /**
     * Get the username of the landlord.
     */
    public function getUsernameAttribute()
    {
        return $this->user ? $this->user->username : null;
    }
}