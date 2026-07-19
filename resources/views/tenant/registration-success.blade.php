@extends('layouts.guest')

@section('title', 'Registration Successful')

@section('content')

<div class="min-h-screen bg-slate-100 py-12">

    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            <!-- Header -->
            <div class="bg-indigo-600 px-8 py-8 text-center">
                <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center">
                    <x-heroicon-o-check-circle class="w-12 h-12 text-indigo-600"/>
                </div>
                <h1 class="mt-5 text-3xl font-bold text-white">
                    Registration Successful
                </h1>
                <p class="mt-2 text-indigo-100">
                    Thank you for registering successfully.
                </p>
            </div>

            <!-- Body -->
            <div class="p-8">

                <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-6 text-center">
                    <p class="text-sm text-slate-500 uppercase tracking-wide">
                        Your Tenant Code
                    </p>
                    <h2 class="mt-3 text-4xl font-extrabold text-indigo-700 tracking-widest">
                        {{ $tenant->tenant_code }}
                    </h2>
                    <p class="mt-4 text-slate-600">
                        Please save this Tenant Code carefully.
                        You will need it whenever your landlord requests it
                        or when checking your payment history.
                    </p>
                </div>

                <!-- Registration Details -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="rounded-xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">
                            Full Name
                        </p>
                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $tenant->name }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">
                            Phone Number
                        </p>
                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $tenant->phone }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">
                            Monthly Rent
                        </p>
                        <p class="mt-1 font-semibold text-slate-800">
                            MK {{ number_format($tenant->monthly_rent) }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">
                            Property
                        </p>
                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $tenant->property->name }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">
                            Registration Date
                        </p>
                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $tenant->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="mt-8 rounded-xl border border-yellow-200 bg-yellow-50 p-5">
                    <div class="flex gap-3">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-yellow-600"/>
                        <div>
                            <h3 class="font-semibold text-yellow-800">
                                Important
                            </h3>
                            <p class="mt-2 text-sm text-yellow-700">
                                This is the only time your Tenant Code will be displayed
                                after registration. Please print this page or take a
                                screenshot before closing it.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-8 flex flex-col md:flex-row gap-4">
                    <button
                        onclick="window.print()"
                        class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl font-semibold transition">

                        Print Registration Slip

                    </button>

                    <a href="{{ route('landlord.tenants.index') }}"
                       class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center py-3 rounded-xl font-semibold transition">

                        Go to Dashboard

                    </a>

                </div>

                <!-- Finish -->
                <div class="mt-6 text-center">
                    <a href="/"
                       class="text-slate-600 hover:text-slate-800 font-medium">

                        Finish

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection