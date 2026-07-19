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
        'monthly_rent',
        'max_tenants',
        'registration_token',
        'status',
    ];

    protected $casts = [
        'monthly_rent' => 'decimal:2',
        'max_tenants' => 'integer',
        'status' => 'boolean',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Check if property has reached maximum tenants
     */
    public function isFull()
    {
        return $this->tenants()->count() >= $this->max_tenants;
    }

    /**
     * Get available slots for tenants
     */
    public function availableSlots()
    {
        $current = $this->tenants()->count();
        $max = $this->max_tenants ?? 0;
        return max(0, $max - $current);
    }

    /**
     * Get current tenant count
     */
    public function currentTenantCount()
    {
        return $this->tenants()->count();
    }

    protected static function booted()
    {
        static::creating(function ($property) {
            if (!$property->registration_token) {
                $property->registration_token = Str::random(40);
            }
            if (!$property->monthly_rent) {
                $property->monthly_rent = 0;
            }
            if (!$property->max_tenants) {
                $property->max_tenants = 10;
            }
            if (!isset($property->status)) {
                $property->status = true;
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

    /**
     * Get formatted monthly rent with currency
     */
    public function getFormattedRentAttribute()
    {
        return 'MK ' . number_format($this->monthly_rent ?? 0);
    }

    /**
     * Get tenant occupancy status text
     */
    public function getOccupancyStatusAttribute()
    {
        if ($this->max_tenants == 0) return 'No Limit';
        $current = $this->currentTenantCount();
        $max = $this->max_tenants;
        return "{$current}/{$max}";
    }

    /**
     * Get occupancy percentage
     */
    public function getOccupancyPercentageAttribute()
    {
        if ($this->max_tenants == 0) return 0;
        return round(($this->currentTenantCount() / $this->max_tenants) * 100);
    }
}