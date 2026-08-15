@extends('layouts.guest')

@section('content')

<div class="min-h-screen bg-[#F8FAFC] py-8 px-4">

    <div class="max-w-md mx-auto">

        <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-[#E5E7EB] bg-red-50">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-[#111827]">Registration Closed</h1>
                        <p class="text-xs text-[#6B7280] mt-0.5">
                            The landlord has temporarily closed registration for this property.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Property Information -->
            <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E5E7EB]">
                <div class="flex flex-col gap-1 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-[#6B7280] flex-shrink-0">Property:</span>
                        <span class="font-medium text-[#111827] text-right break-anywhere">{{ $property->name }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-[#6B7280] flex-shrink-0">Rent:</span>
                        <span class="font-medium text-[#111827] text-right">MK {{ number_format($property->monthly_rent ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Message -->
            <div class="px-6 py-8 text-center">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-[#6B7280]">
                        Please contact the landlord if you believe this is an error.
                    </p>
                </div>

                <div class="mt-6">
                    <p class="text-xs text-[#6B7280]">
                        <span class="font-medium">Registration Status:</span>
                        <span class="text-red-600 font-medium">🔴 Closed</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-[#E5E7EB] bg-[#F8FAFC] px-6 py-3">
                <a href="{{ route('home') }}" 
                   class="block w-full text-center bg-[#0F172A] hover:bg-[#1a2a4a] text-white py-2 rounded-lg font-medium text-sm transition">
                    Return Home
                </a>
            </div>

        </div>

    </div>

</div>

@endsection