@extends('layouts.guest')

@section('title', 'Property Full')

@section('content')

<div class="min-h-screen bg-slate-100 py-8 sm:py-12 px-4">

    <div class="max-w-2xl mx-auto">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-4 sm:px-8 py-5 sm:py-6 border-b border-slate-200">
                <div class="flex items-start sm:items-center gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                        <x-heroicon-o-exclamation-triangle class="w-7 h-7 text-red-600"/>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">
                            Property Full
                        </h1>
                        <p class="text-slate-500 mt-1">
                            This property has reached maximum capacity.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 text-center">
                <div class="rounded-xl border border-red-200 bg-red-50 p-6">
                    <p class="text-red-700">
                        <strong>{{ $property->name }}</strong> has reached its maximum capacity of 
                        <strong>{{ $property->max_tenants }}</strong> tenants.
                    </p>
                    <p class="mt-2 text-red-600 text-sm">
                        Please contact the landlord for assistance.
                    </p>
                </div>

                <div class="mt-6">
                    <a href="/" class="text-slate-600 hover:text-slate-800 font-medium">
                        Go to Home
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection