@extends('layouts.landlord')

@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('content')

<div class="space-y-6">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-[#F3F4F6] border border-[#E5E7EB] text-[#111827] px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-[#F3F4F6] border border-[#E5E7EB] text-[#111827] px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header with Add Tenant Button --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-[#111827]">Tenants</h2>
            <p class="text-sm text-[#6B7280] mt-1">Manage your tenants.</p>
        </div>

        <a href="{{ route('landlord.tenants.create') }}"
           class="bg-[#0F172A] hover:bg-[#1a2a4a] text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2 w-full sm:w-auto justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Tenant
        </a>
    </div>

</div>

@endsection