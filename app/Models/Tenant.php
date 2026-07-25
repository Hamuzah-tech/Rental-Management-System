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
     * Normalize Malawi phone numbers to local format
     * Converts +265XXXXXXXX to 0XXXXXXXXX
     */
    public static function normalizePhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-digit characters
        $cleaned = preg_replace('/\D/', '', $phone);

        // If it starts with 265 (international format without +)
        if (str_starts_with($cleaned, '265')) {
            $cleaned = '0' . substr($cleaned, 3);
        }

        // Ensure it starts with 0 and has 10 digits total (Malawi local format)
        if (strlen($cleaned) === 10 && str_starts_with($cleaned, '0')) {
            return $cleaned;
        }

        // If it's 9 digits (missing leading 0), add it
        if (strlen($cleaned) === 9) {
            return '0' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Validate if a phone number is a valid Malawi number
     */
    public static function isValidMalawiPhone(?string $phone): bool
    {
        if (empty($phone)) {
            return false;
        }

        $normalized = self::normalizePhoneNumber($phone);
        
        if (!$normalized) {
            return false;
        }

        // Must be exactly 10 digits starting with 0
        // Valid prefixes: 099, 088, 098
        return preg_match('/^0(99|88|98)[0-9]{7}$/', $normalized) === 1;
    }

    /**
     * Setter for phone - automatically normalize before saving
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = self::normalizePhoneNumber($value);
    }

    /**
     * Getter for phone - return normalized format
     */
    public function getPhoneAttribute($value)
    {
        return $value;
    }

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

    /**
     * Check if tenant has paid for a specific month.
     */
    public function hasPaidForMonth(string $month): bool
    {
        return $this->payments()
            ->where('status', 'Approved')
            ->where(function ($query) use ($month) {
                $query->where('payment_month', 'LIKE', $month . '%')
                      ->orWhere('payment_month', 'LIKE', '%,' . $month . '%')
                      ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%');
            })
            ->exists();
    }

    /**
     * Check if tenant is unpaid for a specific month.
     */
    public function isUnpaidForMonth(string $month): bool
    {
        return !$this->hasPaidForMonth($month);
    }

    /**
     * Scope to get tenants who paid for a specific month.
     */
    public function scopePaidForMonth($query, string $month)
    {
        return $query->whereHas('payments', function ($q) use ($month) {
            $q->where('status', 'Approved')
              ->where(function ($subQuery) use ($month) {
                  $subQuery->where('payment_month', 'LIKE', $month . '%')
                           ->orWhere('payment_month', 'LIKE', '%,' . $month . '%')
                           ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%');
              });
        });
    }

    /**
     * Scope to get tenants who are unpaid for a specific month.
     */
    public function scopeUnpaidForMonth($query, string $month)
    {
        return $query->whereDoesntHave('payments', function ($q) use ($month) {
            $q->where('status', 'Approved')
              ->where(function ($subQuery) use ($month) {
                  $subQuery->where('payment_month', 'LIKE', $month . '%')
                           ->orWhere('payment_month', 'LIKE', '%,' . $month . '%')
                           ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%');
              });
        });
    }

    /**
     * Check if a phone number already exists in a specific property
     */
    public static function phoneExistsInProperty(string $phone, int $propertyId, ?int $excludeTenantId = null): bool
    {
        $normalized = self::normalizePhoneNumber($phone);
        
        if (!$normalized) {
            return false;
        }

        $query = self::where('property_id', $propertyId)
            ->where('phone', $normalized);

        if ($excludeTenantId) {
            $query->where('id', '!=', $excludeTenantId);
        }

        return $query->exists();
    }
}