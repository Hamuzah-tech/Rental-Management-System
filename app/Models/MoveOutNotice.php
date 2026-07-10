<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoveOutNotice extends Model
{
    protected $fillable = [

        'tenant_id',

        'notice_type',

        'semester',

        'specific_date',

        'comment',

        'status',

        'submitted_at',

        'confirmed_at',

    ];

    protected $casts = [

        'submitted_at' => 'datetime',

        'confirmed_at' => 'datetime',

        'specific_date' => 'date',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}