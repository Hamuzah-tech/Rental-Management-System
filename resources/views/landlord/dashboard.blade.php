@extends('layouts.landlord')

@section('title','Landlord')

{{-- Remove the page heading --}}
@section('page-title','')

@section('content')

<div class="space-y-4">

    <!-- Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <!-- Properties -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide">
                        Hostels
                    </p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ number_format((int)$properties) }}
                    </h3>
                </div>

                <div class="w-10 h-10 rounded-lg bg-[#ca0251]/10 flex items-center justify-center">
                    <x-heroicon-o-building-office class="w-5 h-5 text-[#ca0251]"/>
                </div>
            </div>
        </div>

        <!-- Tenants -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide">
                        Tenants
                    </p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ number_format((int)$tenants) }}
                    </h3>
                </div>

                <div class="w-10 h-10 rounded-lg bg-[#ca0251]/10 flex items-center justify-center">
                    <x-heroicon-o-user-group class="w-5 h-5 text-[#ca0251]"/>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection