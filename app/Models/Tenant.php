<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
   protected $fillable = [

    'property_id',
    'tenant_code',
    'name',
    'phone',
    'email',
    'monthly_rent',
    'move_in_date',
    'status',

];


    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}