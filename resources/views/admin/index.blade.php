@extends('layouts.admin')

@section('title','Dashboard')

@section('page-title','Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">


    <x-admin.stat-card
        title="Landlords"
        value="{{ $totalLandlords }}"
        color="blue">

        <x-slot:icon>
            <x-heroicon-o-users class="w-8 h-8 text-blue-600"/>
        </x-slot:icon>

    </x-admin.stat-card>



    <x-admin.stat-card
        title="Properties"
        value="{{ $totalProperties }}"
        color="emerald">

        <x-slot:icon>
            <x-heroicon-o-building-office class="w-8 h-8 text-emerald-600"/>
        </x-slot:icon>

    </x-admin.stat-card>



    <x-admin.stat-card
        title="Tenants"
        value="{{ $totalTenants }}"
        color="amber">

        <x-slot:icon>
            <x-heroicon-o-user-group class="w-8 h-8 text-amber-600"/>
        </x-slot:icon>

    </x-admin.stat-card>



    <x-admin.stat-card
        title="Pending Payments"
        value="{{ $pendingPayments }}"
        color="red">

        <x-slot:icon>
            <x-heroicon-o-banknotes class="w-8 h-8 text-red-600"/>
        </x-slot:icon>

    </x-admin.stat-card>


</div>


@endsection