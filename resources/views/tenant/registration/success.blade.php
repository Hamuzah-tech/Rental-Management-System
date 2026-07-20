<!-- resources/views/tenant/registration/success.blade.php -->

@extends('layouts.guest')

@section('title', 'Registration Successful')

@section('content')
<div class="min-h-screen bg-slate-100 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            
            <!-- Header -->
            <div class="px-8 py-6 border-b border-slate-200">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Registration Successful
                    </h1>
                    <p class="text-slate-500 mt-1">
                        Welcome to {{ $tenant->property->name }}
                    </p>
                </div>
            </div>

            <div class="p-8 space-y-6">
                
                <!-- Success Message -->
                <div class="rounded-xl border border-green-200 bg-green-50 p-6">
                    <p class="text-green-700 font-medium">
                        Your registration has been completed successfully.
                    </p>
                    <p class="text-green-600 text-sm mt-1">
                        You are now a registered tenant at <strong>{{ $tenant->property->name }}</strong>.
                        Keep and use your tenant code to record and view payments.
                    </p>
                </div>

                <!-- Tenant Details -->
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3 font-medium text-slate-600 w-1/3">Tenant Code</td>
                                <td class="px-4 py-3 text-slate-800 font-mono font-bold">
                                    {{ $tenant->tenant_code }}
                                </td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3 font-medium text-slate-600">Full Name</td>
                                <td class="px-4 py-3 text-slate-800">{{ $tenant->name }}</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3 font-medium text-slate-600">Email</td>
                                <td class="px-4 py-3 text-slate-800">{{ $tenant->email }}</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3 font-medium text-slate-600">Phone</td>
                                <td class="px-4 py-3 text-slate-800">{{ $tenant->phone }}</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3 font-medium text-slate-600">Property</td>
                                <td class="px-4 py-3 text-slate-800">{{ $tenant->property->name }}</td>
                            </tr>
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3 font-medium text-slate-600">Monthly Rent</td>
                                <td class="px-4 py-3 text-slate-800">
                                    MK {{ number_format($tenant->monthly_rent ?? 0, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-600">Move In Date</td>
                                <td class="px-4 py-3 text-slate-800">
                                    {{ $tenant->move_in_date ? date('F d, Y', strtotime($tenant->move_in_date)) : 'N/A' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <a href="/" 
                       class="w-full text-center border border-slate-200 text-slate-600 hover:bg-slate-50 px-6 py-3 rounded-xl transition">
                        Go to Home
                    </a>
                </div>

                <p class="text-xs text-slate-400 text-center pt-2">
                    Please keep your tenant code safe. You will need it to access your payment history.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection