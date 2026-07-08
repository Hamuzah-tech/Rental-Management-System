<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Tenant;


class DashboardController extends Controller
{


public function index()
{

    $landlord = Auth::id();


    $properties = Property::where(
        'landlord_id',
        $landlord
    )->count();



    $tenants = Tenant::whereHas(
        'property',
        function($q) use ($landlord){

            $q->where(
                'landlord_id',
                $landlord
            );

        }
    )->count();



    $activeTenants = Tenant::whereHas(
        'property',
        function($q) use ($landlord){

            $q->where(
                'landlord_id',
                $landlord
            );

        }
    )
    ->where('status','Active')
    ->count();



    return view(
        'landlord.dashboard',
        compact(
            'properties',
            'tenants',
            'activeTenants'
        )
    );

}


}