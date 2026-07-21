<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'tenant_code',
        'tenant_name'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the months as an array
     */
    public function getMonthsArrayAttribute()
    {
        if (empty($this->payment_month)) {
            return [];
        }
        return explode(',', $this->payment_month);
    }

    /**
     * Get the month count
     */
    public function getMonthCountAttribute()
    {
        return count($this->months_array);
    }

    /**
     * Get formatted month range
     */
    public function getMonthRangeAttribute()
    {
        $months = $this->months_array;
        if (empty($months)) {
            return 'N/A';
        }

        if (count($months) === 1) {
            return \Carbon\Carbon::createFromFormat('Y-m', trim($months[0]))->format('F Y');
        }

        $first = \Carbon\Carbon::createFromFormat('Y-m', trim($months[0]))->format('M Y');
        $last = \Carbon\Carbon::createFromFormat('Y-m', trim(end($months)))->format('M Y');
        
        return $first . ' → ' . $last . ' (' . count($months) . ' months)';
    }

    /**
     * Get per month amount
     */
    public function getPerMonthAmountAttribute()
    {
        $count = $this->month_count;
        if ($count <= 0) {
            return $this->amount;
        }
        return $this->amount / $count;
    }
}