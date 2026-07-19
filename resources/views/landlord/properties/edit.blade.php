@extends('layouts.landlord')

@section('title','Edit Property')

@section('content')

<div class="max-w-3xl mx-auto">
    
<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

    <!-- Header -->
    <div class="px-6 py-4 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                <x-heroicon-o-pencil-square class="w-5 h-5 text-slate-400"/>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    Edit Hostel
                </h2>
                <p class="text-sm text-slate-500">
                    Update Hostel information.
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('landlord.properties.update',$property) }}">
        @csrf
        @method('PUT')

        <div class="p-6 space-y-5">

            <!-- Property Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Hostel Name <span class="text-red-500">*</span>
                </label>
                <input
                    name="name"
                    value="{{ old('name',$property->name) }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm"
                    required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Address
                    <span class="text-slate-400 font-normal">(Optional)</span>
                </label>
                <input
                    name="address"
                    value="{{ old('address',$property->address) }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm">
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Monthly Rent -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Monthly Rent (MK) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-medium">MK</span>
                    <input
                        type="text"
                        id="monthlyRent"
                        name="monthly_rent"
                        value="{{ old('monthly_rent', number_format($property->monthly_rent ?? 0)) }}"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm pl-12"
                        required>
                </div>
                <p class="text-xs text-slate-500 mt-1">Enter the monthly rent amount for this property</p>
                @error('monthly_rent')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Max Tenants -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Maximum Tenants <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    name="max_tenants"
                    value="{{ old('max_tenants', $property->max_tenants ?? 10) }}"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm"
                    min="1"
                    required>
                <p class="text-xs text-slate-500 mt-1">Maximum number of tenants allowed for this property</p>
                <p class="text-xs text-blue-600 mt-1">
                    Current tenants: {{ $property->currentTenantCount() }} / {{ $property->max_tenants ?? 0 }}
                </p>
                @error('max_tenants')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Description
                    <span class="text-slate-400 font-normal">(Optional)</span>
                </label>
                <textarea
                    name="description"
                    rows="4"
                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm">{{ old('description',$property->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <!-- Footer -->
        <div class="border-t border-slate-200 px-6 py-4 flex justify-end gap-3">
            <a href="{{ route('landlord.properties.index') }}"
               class="px-5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm transition">
                Cancel
            </a>

            <button
                class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-xl text-sm transition flex items-center gap-2">
                <x-heroicon-o-check class="w-4 h-4"/>
                Update Hostel
            </button>
        </div>

    </form>

</div>
</div>

@endsection

@push('scripts')
<script>
    // Format number with commas
    function formatNumberWithCommas(number) {
        if (!number && number !== 0) return '';
        const num = number.toString().replace(/,/g, '');
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function handleRentInput(e) {
        const input = e.target;
        let value = input.value.replace(/,/g, '').replace(/[^\d]/g, '');
        
        if (value === '') {
            input.value = '';
            input.dataset.rawValue = '';
            return;
        }
        
        const numericValue = parseFloat(value);
        if (!isNaN(numericValue)) {
            input.value = formatNumberWithCommas(numericValue);
            input.dataset.rawValue = numericValue;
        }
    }

    function handleRentBlur(e) {
        const input = e.target;
        let value = input.value.replace(/,/g, '').replace(/[^\d]/g, '');
        
        if (value !== '') {
            const numericValue = parseFloat(value);
            if (!isNaN(numericValue)) {
                input.value = formatNumberWithCommas(numericValue);
                input.dataset.rawValue = numericValue;
            }
        }
    }

    document.querySelector('form')?.addEventListener('submit', function(e) {
        const rentInput = document.getElementById('monthlyRent');
        if (rentInput) {
            const rawValue = rentInput.dataset.rawValue || rentInput.value.replace(/,/g, '');
            rentInput.value = rawValue;
        }
    });

    const rentInput = document.getElementById('monthlyRent');
    if (rentInput) {
        rentInput.addEventListener('input', handleRentInput);
        rentInput.addEventListener('blur', handleRentBlur);
    }
</script>
@endpush