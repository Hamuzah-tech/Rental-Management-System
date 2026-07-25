@extends('layouts.landlord')

@section('title','Edit Tenant')
@section('page-title','Edit Tenant')

@section('content')

<div class="max-w-5xl mx-auto">

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

    <!-- Header - More Compact -->
    <div class="px-5 py-3 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                <x-heroicon-o-pencil-square class="w-4 h-4 text-slate-400"/>
            </div>
            <div>
                <h2 class="text-base font-semibold text-slate-800">
                    Edit Tenant
                </h2>
                <p class="text-xs text-slate-500">
                    Update tenant information.
                </p>
            </div>
        </div>
    </div>

    <!-- Error Display -->
    @if($errors->any())
        <div class="mx-5 mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
            <ul class="text-red-600 text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('landlord.tenants.update', $tenant) }}" id="editTenantForm">
        @csrf
        @method('PUT')

        <div class="p-5">

            <!-- Two Column Layout for Compactness -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">

                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $tenant->name) }}"
                        class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm py-1.5 px-3 @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $tenant->email) }}"
                        class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm py-1.5 px-3 @error('email') border-red-500 @enderror"
                        required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone', $tenant->phone) }}"
                        class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm py-1.5 px-3 @error('phone') border-red-500 @enderror"
                        pattern="[0-9]{10,15}"
                        required>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Property -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Hostel <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="property_id"
                        class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm py-1.5 px-3 @error('property_id') border-red-500 @enderror"
                        required>
                        @foreach($properties as $property)
                            <option
                                value="{{ $property->id }}"
                                @if($tenant->property_id == $property->id) selected @endif
                            >
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Monthly Rent -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Monthly Rent (MK) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs font-medium">MK</span>
                        <input
                            type="number"
                            name="monthly_rent"
                            value="{{ old('monthly_rent', $tenant->monthly_rent) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm py-1.5 pl-8 pr-3 @error('monthly_rent') border-red-500 @enderror"
                            step="0.01"
                            min="0"
                            required>
                    </div>
                    @error('monthly_rent')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Move In Date -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Move In Date <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="move_in_date"
                        value="{{ old('move_in_date', $tenant->move_in_date ? $tenant->move_in_date->format('Y-m-d') : date('Y-m-d')) }}"
                        class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm py-1.5 px-3 @error('move_in_date') border-red-500 @enderror"
                        required>
                    @error('move_in_date')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status (Hidden) -->
                <input type="hidden" name="status" value="{{ $tenant->status }}">

            </div>

        </div>

        <!-- Footer - Compact -->
        <div class="border-t border-slate-200 px-5 py-3 flex justify-end gap-2.5">
            <a href="{{ route('landlord.properties.show', $tenant->property_id) }}"
               class="px-4 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-sm">
                Cancel
            </a>

            <button
                type="submit"
                class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-1.5 rounded-lg transition flex items-center gap-2 text-sm">
                <x-heroicon-o-check class="w-4 h-4"/>
                Update Tenant
            </button>
        </div>

    </form>

</div>
</div>

<!-- Success Modal -->
@if(session('success'))
<div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6 transform transition-all scale-100">
        <div class="flex flex-col items-center text-center">
            <!-- Success Icon -->
            <div class="w-16 h-16 rounded-full bg-[#F3F4F6] flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-[#0F172A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h2 class="text-xl font-bold text-[#111827] mb-2">Tenant Updated Successfully</h2>
            <p class="text-sm text-[#6B7280] mb-6">
                {{ session('success') }}
            </p>

            <a href="{{ route('landlord.properties.show', $tenant->property_id) }}"
               class="w-full inline-flex items-center justify-center rounded-lg bg-[#0F172A] hover:bg-[#1a2a4a] text-white px-6 py-2.5 text-sm font-medium transition">
                Continue
            </a>
        </div>
    </div>
</div>

<style>
    #successModal {
        animation: modalFadeIn 0.3s ease-out;
    }
    #successModal > div {
        animation: modalSlideIn 0.3s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successModal = document.getElementById('successModal');
        if (successModal) {
            // Auto-close after 5 seconds
            setTimeout(function() {
                successModal.style.opacity = '0';
                successModal.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    successModal.style.display = 'none';
                }, 500);
            }, 5000);

            // Close on outside click
            successModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.opacity = '0';
                    this.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => {
                        this.style.display = 'none';
                    }, 300);
                }
            });
        }
    });
</script>
@endif

@endsection