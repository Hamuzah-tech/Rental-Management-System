<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        // Total landlords
        $totalLandlords = User::role('Landlord')->count();

        // Total properties
        $totalProperties = Property::count();

        // Total tenants
        $totalTenants = Tenant::count();

        return view('admin.index', compact(
            'totalLandlords',
            'totalProperties',
            'totalTenants'
        ));
    }
}