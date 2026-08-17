@extends('layouts.landlord')

@section('title','Edit Hostel')
@section('page-title','Edit Hostel')

@section('content')

<div class="max-w-4xl mx-auto">

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

    <!-- Header - Compact -->
    <div class="px-5 py-3 border-b border-slate-200">
        <h2 class="text-base font-semibold text-slate-800">
            Edit Hostel
        </h2>
        <p class="text-xs text-slate-500">
            Update hostel information.
        </p>
    </div>

    <form method="POST" action="{{ route('landlord.properties.update', $property) }}" id="editPropertyForm">
        @csrf
        @method('PUT')

        <div class="p-5">

            <!-- Two Column Layout for Compactness -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">

                <!-- Hostel Name -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Hostel Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', e($property->name)) }}"
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm py-1.5 px-3 @error('name') border-red-500 @enderror"
                        required
                        maxlength="255">
                    @error('name')
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
                            type="text"
                            id="monthlyRent"
                            name="monthly_rent"
                            value="{{ old('monthly_rent', number_format((float)($property->monthly_rent ?? 0))) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm py-1.5 pl-8 pr-3 @error('monthly_rent') border-red-500 @enderror"
                            required
                            maxlength="15">
                    </div>
                    @error('monthly_rent')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Tenants -->
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Max Tenants <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="max_tenants"
                        value="{{ old('max_tenants', (int)($property->max_tenants ?? 10)) }}"
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm py-1.5 px-3 @error('max_tenants') border-red-500 @enderror"
                        min="1"
                        max="999"
                        required>
                    <p class="text-xs text-slate-500 mt-0.5">Current tenants: {{ (int)$property->currentTenantCount() }} / {{ (int)($property->max_tenants ?? 0) }}</p>
                    @error('max_tenants')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

            </div>

        </div>

        <!-- Footer - Compact -->
        <div class="border-t border-slate-200 px-5 py-3 page-actions">
            <a href="{{ route('landlord.properties.index') }}"
               class="px-4 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-[#ca0251]/10 hover:text-[#ca0251] hover:border-[#ca0251] transition text-sm w-full sm:w-auto text-center">
                Cancel
            </a>

            <button
                type="submit"
                class="bg-[#ca0251] hover:bg-[#a80244] text-white px-5 py-1.5 rounded-lg transition flex items-center justify-center gap-2 text-sm w-full sm:w-auto">
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
        // Only allow digits and commas
        let value = input.value.replace(/,/g, '').replace(/[^\d]/g, '');
        
        if (value === '') {
            input.value = '';
            input.dataset.rawValue = '';
            return;
        }
        
        const numericValue = parseFloat(value);
        if (!isNaN(numericValue) && numericValue >= 0) {
            input.value = formatNumberWithCommas(numericValue);
            input.dataset.rawValue = numericValue;
        }
    }

    function handleRentBlur(e) {
        const input = e.target;
        let value = input.value.replace(/,/g, '').replace(/[^\d]/g, '');
        
        if (value !== '') {
            const numericValue = parseFloat(value);
            if (!isNaN(numericValue) && numericValue >= 0) {
                input.value = formatNumberWithCommas(numericValue);
                input.dataset.rawValue = numericValue;
            }
        }
    }

    // Handle form submission - remove commas before submit
    document.getElementById('editPropertyForm')?.addEventListener('submit', function(e) {
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