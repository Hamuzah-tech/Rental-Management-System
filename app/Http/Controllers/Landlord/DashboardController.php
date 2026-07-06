<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show landlord dashboard
     */
    public function index()
    {
        return view('landlord.dashboard');
    }
}