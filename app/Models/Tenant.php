<?php
// app/Models/Tenant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_code',
        'property_id',
        'name',
        'email',
        'phone',
        'monthly_rent',
        'move_in_date',
        'status',
    ];

    protected $casts = [
        'monthly_rent' => 'decimal:2',
        'move_in_date' => 'date',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Tenant belongs to a property.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Tenant has many payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Tenant has many move-out notices.
     */
    public function moveOutNotices(): HasMany
    {
        return $this->hasMany(MoveOutNotice::class);
    }

    /**
     * Get formatted monthly rent with currency.
     */
    public function getFormattedRentAttribute(): string
    {
        return 'MK ' . number_format($this->monthly_rent ?? 0, 2);
    }

    /**
     * Get the effective rent (tenant's rent or property default).
     */
    public function getEffectiveRentAttribute(): float
    {
        return $this->monthly_rent ?? $this->property->monthly_rent ?? 0;
    }

    /**
     * Check if tenant has a custom rent different from property default.
     */
    public function hasCustomRent(): bool
    {
        if (!$this->property) return false;
        return $this->monthly_rent != $this->property->monthly_rent;
    }
}