<?php
// app/Models/Tenant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes; // ← ADD THIS

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

    protected $dates = ['deleted_at']; // ← ADD THIS

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
}