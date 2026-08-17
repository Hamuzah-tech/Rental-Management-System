@extends('layouts.landlord')

@section('title','Add Hostel')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

        <!-- Header - More Compact -->
        <div class="px-5 py-3 border-b border-slate-200">
            <h2 class="text-base font-semibold text-slate-800">
                Add Hostel
            </h2>
        </div>

        <form method="POST" action="{{ route('landlord.properties.store') }}" id="propertyForm">
            @csrf

            <div class="p-5 space-y-4">

                <!-- Hostel Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Hostel Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Monthly Rent & Max Tenants - Side by Side -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Monthly Rent -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Monthly Rent (MK) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-medium text-sm">MK</span>
                            <input
                                type="text"
                                id="monthlyRent"
                                name="monthly_rent"
                                value="{{ old('monthly_rent') ? number_format((float)str_replace(',', '', old('monthly_rent'))) : '' }}"
                                class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm pl-9 @error('monthly_rent') border-red-500 @enderror"
                                required>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Monthly rent amount</p>
                        @error('monthly_rent')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Tenants -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            Max Tenants <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="max_tenants"
                            value="{{ old('max_tenants', 10) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm @error('max_tenants') border-red-500 @enderror"
                            min="1"
                            required>
                        <p class="text-xs text-slate-500 mt-1">Maximum tenants allowed</p>
                        @error('max_tenants')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <!-- Footer - More Compact -->
            <div class="border-t border-slate-200 px-5 py-3 page-actions">
                <a href="{{ route('landlord.properties.index') }}"
                   class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-[#ca0251]/10 hover:text-[#ca0251] hover:border-[#ca0251] text-sm transition w-full sm:w-auto text-center">
                    Cancel
                </a>

                <button
                    type="submit"
                    class="bg-[#ca0251] hover:bg-[#a80244] text-white px-5 py-2 rounded-lg text-sm transition flex items-center justify-center gap-2 w-full sm:w-auto">
                    <x-heroicon-o-check class="w-4 h-4"/>
                    Save
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

    // Handle rent input with commas
    function handleRentInput(e) {
        const input = e.target;
        const cursorPosition = input.selectionStart;
        const oldLength = input.value.length;
        
        // Remove non-numeric characters
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
            
            // Adjust cursor position
            const newLength = input.value.length;
            const diff = newLength - oldLength;
            input.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
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

    // Handle form submission - REMOVE COMMAS BEFORE SUBMIT
    document.getElementById('propertyForm')?.addEventListener('submit', function(e) {
        const rentInput = document.getElementById('monthlyRent');
        if (rentInput) {
            // Remove commas for form submission
            const rawValue = rentInput.value.replace(/,/g, '');
            rentInput.value = rawValue;
        }
    });

    // Initialize rent input
    const rentInput = document.getElementById('monthlyRent');
    if (rentInput) {
        rentInput.addEventListener('input', handleRentInput);
        rentInput.addEventListener('blur', handleRentBlur);
    }
</script>
@endpush