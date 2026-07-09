<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [

        'tenant_id',

        'payment_month',

        'amount',

        'screenshot',

        'status',

        'remarks',

        'submitted_at',

        'approved_at',

        'approved_by',

    ];

    protected $casts = [

        'submitted_at' => 'datetime',

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
}