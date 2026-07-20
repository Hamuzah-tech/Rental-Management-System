@extends('layouts.landlord')

@section('title','Add Property')

@section('content')

<div class="max-w-3xl mx-auto">

    <!-- Display Success Message -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Display All Errors -->
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                <x-heroicon-o-building-office class="w-6 h-6 text-slate-400"/>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Add Hostel
                </h2>
            </div>
        </div>

        <form method="POST" action="{{ route('landlord.properties.store') }}" id="propertyForm">
            @csrf

            <div class="p-6 space-y-5">

                <!-- Property Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Hostel Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm @error('name') border-red-500 @enderror"
                        placeholder="Example: Sunrise Hostel"
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
                        type="text"
                        name="address"
                        value="{{ old('address') }}"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm @error('address') border-red-500 @enderror"
                        placeholder="Example: Chikanda">
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
                            value="{{ old('monthly_rent') ? number_format((float)str_replace(',', '', old('monthly_rent'))) : '' }}"
                            class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm pl-12 @error('monthly_rent') border-red-500 @enderror"
                            placeholder="0"
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
                        value="{{ old('max_tenants', 10) }}"
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm @error('max_tenants') border-red-500 @enderror"
                        placeholder="Example: 10"
                        min="1"
                        required>
                    <p class="text-xs text-slate-500 mt-1">Maximum number of tenants allowed for this property</p>
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
                        class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm @error('description') border-red-500 @enderror"
                        placeholder="Hostel description">{{ old('description') }}</textarea>
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
                    type="submit"
                    class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2 rounded-xl text-sm transition flex items-center gap-2">
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