<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Payment extends Model
{
    protected $fillable = [
        'tenant_id',
        'payment_month',
        'amount',
        'status',
        'screenshot',
        'remarks',
        'approved_by',
        'approved_at',
        // 'tenant_code', // Remove if not used - these should come from the tenant relationship
        // 'tenant_name', // Remove if not used - these should come from the tenant relationship
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the payment.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who approved the payment.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the months as an array.
     */
    public function getMonthsArrayAttribute(): array
    {
        if (empty($this->payment_month)) {
            return [];
        }

        return array_map('trim', explode(',', $this->payment_month));
    }

    /**
     * Get the month count.
     */
    public function getMonthCountAttribute(): int
    {
        return count($this->months_array);
    }

    /**
     * Get formatted month range with exception handling.
     */
    public function getMonthRangeAttribute(): string
    {
        try {
            $months = $this->months_array;
            
            if (empty($months)) {
                return 'N/A';
            }

            if (count($months) === 1) {
                return Carbon::createFromFormat('Y-m', trim($months[0]))->format('F Y');
            }

            $first = Carbon::createFromFormat('Y-m', trim($months[0]))->format('M Y');
            $last = Carbon::createFromFormat('Y-m', trim(end($months)))->format('M Y');
            
            return $first . ' → ' . $last . ' (' . count($months) . ' months)';
            
        } catch (\Exception $e) {
            // Log the error but don't expose it to users
            \Illuminate\Support\Facades\Log::warning('Failed to format payment month range', [
                'payment_id' => $this->id,
                'payment_month' => $this->payment_month,
                'error' => $e->getMessage(),
            ]);

            return 'Invalid date format';
        }
    }

    /**
     * Get per month amount (rounded to 2 decimal places).
     */
    public function getPerMonthAmountAttribute(): float
    {
        $count = $this->month_count;
        
        if ($count <= 0) {
            return (float) $this->amount;
        }
        
        return round($this->amount / $count, 2);
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'MK ' . number_format($this->amount, 2);
    }

    /**
     * Get the formatted per-month amount with currency.
     */
    public function getFormattedPerMonthAmountAttribute(): string
    {
        return 'MK ' . number_format($this->per_month_amount, 2);
    }

    /**
     * Check if this payment covers a specific month.
     */
    public function coversMonth(string $month): bool
    {
        $months = $this->months_array;
        return in_array($month, $months);
    }

    /**
     * Check if this payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if this payment is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }

    /**
     * Check if this payment is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'Rejected';
    }

    /**
     * Get the status label with proper styling.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'Approved' => 'Approved',
            'Rejected' => 'Rejected',
            'Pending' => 'Pending',
            default => ucfirst($this->status ?? 'Unknown'),
        };
    }

    /**
     * Get the status color class for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Approved' => 'green',
            'Rejected' => 'red',
            'Pending' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Scope to get payments for a specific month.
     * Updated to handle all cases consistently.
     */
    public function scopeForMonth($query, string $month)
    {
        return $query->where(function ($q) use ($month) {
            $q->where('payment_month', '=', $month) // Exact match
              ->orWhere('payment_month', 'LIKE', $month . ',%') // At start
              ->orWhere('payment_month', 'LIKE', '%,' . $month . ',%') // In middle
              ->orWhere('payment_month', 'LIKE', '%,' . $month); // At end
        });
    }

    /**
     * Scope to get pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope to get approved payments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope to get rejected payments.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    /**
     * Scope to get payments for a specific tenant.
     */
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure status defaults to 'Pending' when creating
        static::creating(function ($payment) {
            if (empty($payment->status)) {
                $payment->status = 'Pending';
            }
        });
    }

    /**
     * Get the months array as a collection with proper date formatting.
     */
    public function getFormattedMonthsAttribute(): array
    {
        $months = $this->months_array;
        
        return array_map(function ($month) {
            try {
                return Carbon::createFromFormat('Y-m', trim($month))->format('F Y');
            } catch (\Exception $e) {
                return $month;
            }
        }, $months);
    }
}