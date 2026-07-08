<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLandlords = User::role('Landlord')->count();

        $totalProperties = 0;

        $totalTenants = 0;

        $pendingPayments = 0;


        return view('admin.index', compact(
            'totalLandlords',
            'totalProperties',
            'totalTenants',
            'pendingPayments'
        ));
    }
}