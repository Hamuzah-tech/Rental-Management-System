<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{

    protected $fillable = [
        'landlord_id',
        'name',
        'address',
        'description',
        'status',
    ];


    public function landlord()
    {
        return $this->belongsTo(User::class,'landlord_id');
    }


    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

}