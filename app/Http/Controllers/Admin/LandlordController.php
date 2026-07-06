<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class LandlordController extends Controller
{
    public function index()
    {
        $landlords = User::role('Landlord')
            ->latest()
            ->paginate(10);

        return view('admin.landlords.index', compact('landlords'));
    }

    public function create()
    {
        return view('admin.landlords.create');
    }
}