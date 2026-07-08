@extends('layouts.landlord')

@section('title','Landlord Dashboard')

@section('page-title','Dashboard')

@section('content')

<div class="grid grid-cols-3 gap-6">


    <div class="bg-white p-6 rounded-xl shadow">

        <h3>
            Properties
        </h3>

        <p class="text-3xl font-bold">
            {{ $properties }}
        </p>

    </div>



    <div class="bg-white p-6 rounded-xl shadow">

        <h3>
            Tenants
        </h3>

        <p class="text-3xl font-bold">
            {{ $tenants }}
        </p>

    </div>



    <div class="bg-white p-6 rounded-xl shadow">

        <h3>
            Active Tenants
        </h3>

        <p class="text-3xl font-bold">
            {{ $activeTenants }}
        </p>

    </div>


</div>

@endsection