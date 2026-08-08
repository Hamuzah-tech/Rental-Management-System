<?php
// app/Models/Property.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Traits\HasPublicId;

class Property extends Model
{
    use SoftDeletes, HasPublicId;

    protected $fillable = [
        'landlord_id',
        'name',
        'address',
        'description',
        'monthly_rent',
        'max_tenants',
        'registration_token',
        'status',
        'registration_open',
    ];

    protected $casts = [
        'monthly_rent' => 'decimal:2',
        'max_tenants' => 'integer',
        'status' => 'boolean',
        'registration_open' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the landlord that owns the property.
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get the tenants for the property.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Get active tenants (not soft deleted).
     */
    public function activeTenants(): HasMany
    {
        return $this->tenants()->whereNull('deleted_at');
    }

    /**
     * Check if property has reached maximum tenants.
     * Uses cached count to avoid multiple queries.
     */
    public function isFull(): bool
    {
        return $this->activeTenants()->count() >= $this->max_tenants;
    }

    /**
     * Get available slots for tenants.
     */
    public function availableSlots(): int
    {
        $current = $this->activeTenants()->count();
        $max = $this->max_tenants ?? 0;
        return max(0, $max - $current);
    }

    /**
     * Get current active tenant count.
     */
    public function currentTenantCount(): int
    {
        return $this->activeTenants()->count();
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($property) {
            if (blank($property->registration_token)) {
                $property->registration_token = Str::random(40);
            }
            if (blank($property->monthly_rent)) {
                $property->monthly_rent = 0;
            }
            if (blank($property->max_tenants)) {
                $property->max_tenants = 10;
            }
            if (!isset($property->status)) {
                $property->status = true;
            }
            if (!isset($property->registration_open)) {
                $property->registration_open = true;
            }
        });
    }

    /**
     * Get the registration link for this property.
     */
    public function getRegistrationLink(): string
    {
        return route('tenant.registration', ['token' => $this->registration_token]);
    }

    /**
     * Get formatted monthly rent with currency.
     */
    public function getFormattedRentAttribute(): string
    {
        return 'MK ' . number_format($this->monthly_rent ?? 0);
    }

    /**
     * Get tenant occupancy status text.
     */
    public function getOccupancyStatusAttribute(): string
    {
        if ($this->max_tenants == 0) {
            return 'No Limit';
        }
        
        $current = $this->currentTenantCount();
        $max = $this->max_tenants;
        return "{$current}/{$max}";
    }

    /**
     * Get occupancy percentage.
     */
    public function getOccupancyPercentageAttribute(): int
    {
        if ($this->max_tenants == 0) {
            return 0;
        }
        
        return round(($this->currentTenantCount() / $this->max_tenants) * 100);
    }

    /**
     * Get a summary of tenants with their rent details.
     * Renamed for clarity - returns tenants with selected columns.
     */
    public function tenantsSummary(): HasMany
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
        return (float) ($this->monthly_rent ?? 0);
    }

    /**
     * Get rent statistics for this property.
     * Uses SQL aggregations for better performance.
     */
    public function getRentStatsAttribute(): array
    {
        $stats = $this->activeTenants()
            ->selectRaw('
                MIN(monthly_rent) as min,
                MAX(monthly_rent) as max,
                AVG(monthly_rent) as avg,
                COUNT(*) as count
            ')
            ->first();

        return [
            'min' => (float) ($stats->min ?? 0),
            'max' => (float) ($stats->max ?? 0),
            'avg' => (float) ($stats->avg ?? 0),
            'count' => (int) ($stats->count ?? 0),
        ];
    }

    /**
     * Get the total monthly rent revenue from all active tenants.
     */
    public function getTotalMonthlyRevenueAttribute(): float
    {
        return (float) $this->activeTenants()->sum('monthly_rent');
    }

    /**
     * Check if the property is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->status;
    }

    /**
     * Check if registration is open.
     */
    public function isRegistrationOpen(): bool
    {
        return (bool) $this->registration_open;
    }

    /**
     * Scope a query to only include active properties.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include properties with registration open.
     */
    public function scopeRegistrationOpen($query)
    {
        return $query->where('registration_open', true);
    }

    /**
     * Scope a query to only include properties with available slots.
     */
    public function scopeHasAvailableSlots($query)
    {
        return $query->where('max_tenants', '>', function ($subQuery) {
            $subQuery->selectRaw('COUNT(*)')
                ->from('tenants')
                ->whereColumn('tenants.property_id', 'properties.id')
                ->whereNull('tenants.deleted_at');
        });
    }

    /**
     * Scope a query to only include full properties.
     */
    public function scopeFull($query)
    {
        return $query->where('max_tenants', '<=', function ($subQuery) {
            $subQuery->selectRaw('COUNT(*)')
                ->from('tenants')
                ->whereColumn('tenants.property_id', 'properties.id')
                ->whereNull('tenants.deleted_at');
        });
    }

    /**
     * Get the property's address as a formatted string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city ?? null,
            $this->state ?? null,
            $this->postal_code ?? null,
            $this->country ?? null,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if the property has tenants.
     */
    public function hasTenants(): bool
    {
        return $this->activeTenants()->exists();
    }
}