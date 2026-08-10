<?php
// app/Models/Tenant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\Traits\HasPublicId;

class Tenant extends Model
{
    use SoftDeletes, HasPublicId;

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

    /**
     * Get the route key for the model.
     * This tells Laravel to use public_id instead of id for route model binding.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'public_id';
    }

    /**
     * Generate a secure random 6-character alphanumeric tenant code.
     * Uses cryptographically secure random bytes for maximum security.
     * Automatically checks for uniqueness and regenerates if duplicate found.
     * 
     * @return string
     */
    public static function generateUniqueTenantCode(): string
    {
        $maxAttempts = 50;
        $attempts = 0;
        
        do {
            // Generate a cryptographically secure random code
            // Using random_bytes for maximum security (better than rand() or uniqid())
            $bytes = random_bytes(6);
            // Convert to hex and take first 6 characters, then uppercase
            $code = strtoupper(substr(bin2hex($bytes), 0, 6));
            
            // Ensure the code contains at least one letter and one number for better distribution
            // If it's all numbers or all letters, regenerate
            if (!preg_match('/[A-Z]/', $code) || !preg_match('/[0-9]/', $code)) {
                continue;
            }
            
            $attempts++;
            
            // Check if code already exists in database (including soft-deleted records)
            $exists = self::withTrashed()
                ->where('tenant_code', $code)
                ->exists();
                
        } while ($exists && $attempts < $maxAttempts);
        
        // If we've exhausted attempts and still have a duplicate (extremely rare),
        // fallback to a more complex code with timestamp to ensure uniqueness
        if ($exists) {
            $timestamp = now()->timestamp;
            $random = strtoupper(substr(bin2hex(random_bytes(4)), 0, 4));
            $code = strtoupper(substr($timestamp . $random, 0, 6));
        }
        
        return $code;
    }

    /**
     * Generate a secure tenant code with a prefix (for backward compatibility).
     * This method maintains compatibility with existing code that expects a prefix.
     * 
     * @return string
     */
    public static function generateTenantCodeWithPrefix(): string
    {
        return 'TEN-' . self::generateUniqueTenantCode();
    }

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

        // Accept any valid Malawi mobile number starting with 08 or 09
        return preg_match('/^0[89][0-9]{8}$/', $normalized) === 1;
    }

    /**
     * Setter for phone - automatically normalize before saving
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = self::normalizePhoneNumber($value);
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
     * Uses optional() to prevent extra database queries if property isn't loaded.
     */
    public function getEffectiveRentAttribute(): float
    {
        return (float) ($this->monthly_rent ?? optional($this->property)->monthly_rent ?? 0);
    }

    /**
     * Check if tenant has a custom rent different from property default.
     * Uses strict comparison for decimal values.
     */
    public function hasCustomRent(): bool
    {
        if (!$this->property) {
            return false;
        }

        return (float) $this->monthly_rent !== (float) $this->property->monthly_rent;
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

    /**
     * Get the total payments amount for this tenant.
     */
    public function getTotalPaymentsAttribute(): float
    {
        return (float) $this->payments()->where('status', 'Approved')->sum('amount');
    }

    /**
     * Get the total pending payments amount for this tenant.
     */
    public function getPendingPaymentsAttribute(): float
    {
        return (float) $this->payments()->where('status', 'Pending')->sum('amount');
    }

    /**
     * Get the tenant's current status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'Inactive');
    }

    /**
     * Scope to get active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get inactive tenants.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope to get moved out tenants.
     */
    public function scopeMovedOut($query)
    {
        return $query->where('status', 'moved_out');
    }

    /**
     * Check if the tenant is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the tenant is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if the tenant has moved out.
     */
    public function isMovedOut(): bool
    {
        return $this->status === 'moved_out';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate secure tenant code before creating
        static::creating(function ($tenant) {
            if (empty($tenant->tenant_code)) {
                // Use the new secure generator without prefix
                $tenant->tenant_code = self::generateUniqueTenantCode();
            }
        });
    }
}