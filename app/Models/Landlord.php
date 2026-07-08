<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Landlord extends Model
{
    //
}
class Landlord extends Model
{
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'second_phone',
        'password'
    ];
}