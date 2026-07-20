<?php
// app/Models/Property.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use SoftDeletes;

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

    protected $dates = ['deleted_at'];

    // Relationships
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

    /**
     * Get all tenants with their individual rents.
     */
    public function tenantsWithRent()
    {
        return $this->hasMany(Tenant::class)->select([
            'id', 'name', 'email', 'phone', 'monthly_rent', 'status'
        ]);
    }

    /**
     * Get the default rent for new tenants.
     */
    public function getDefaultRentAttribute(): float
    {
        return $this->monthly_rent ?? 0;
    }

    /**
     * Get rent statistics for this property.
     */
    public function getRentStatsAttribute(): array
    {
        $tenants = $this->tenants()->whereNull('deleted_at')->get();
        
        return [
            'min' => $tenants->min('monthly_rent') ?? 0,
            'max' => $tenants->max('monthly_rent') ?? 0,
            'avg' => $tenants->avg('monthly_rent') ?? 0,
            'count' => $tenants->count(),
        ];
    }
}