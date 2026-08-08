@extends('layouts.landlord')

@section('title','Edit Tenant')
@section('page-title','Edit Tenant')

@section('content')

<div class="max-w-5xl mx-auto">

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

    <!-- Header - More Compact -->
    <div class="px-5 py-3 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#ca0251]/10 flex items-center justify-center">
                <x-heroicon-o-pencil-square class="w-4 h-4 text-[#ca0251]"/>
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

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mx-5 mt-3 p-3 bg-[#ca0251]/10 border border-[#ca0251] text-[#ca0251] rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="mx-5 mt-3 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

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
                        value="{{ old('name', e($tenant->name)) }}"
                        maxlength="255"
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm py-1.5 px-3 @error('name') border-red-500 @enderror"
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
                        value="{{ old('email', e($tenant->email)) }}"
                        maxlength="255"
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm py-1.5 px-3 @error('email') border-red-500 @enderror"
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
                        value="{{ old('phone', e($tenant->phone)) }}"
                        maxlength="15"
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm py-1.5 px-3 @error('phone') border-red-500 @enderror"
                        pattern="[0-9+]{10,15}"
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
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm py-1.5 px-3 @error('property_id') border-red-500 @enderror"
                        required>
                        @foreach($properties as $property)
                            <option
                                value="{{ $property->id }}"
                                @if($tenant->property_id == $property->id) selected @endif
                            >
                                {{ e($property->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Monthly Rent - IMPROVED: Numeric only with thousand separators -->
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
                            value="{{ old('monthly_rent', number_format((float)($tenant->monthly_rent ?? 0))) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm py-1.5 pl-8 pr-3 @error('monthly_rent') border-red-500 @enderror"
                            placeholder="e.g. 89,000"
                            inputmode="numeric"
                            autocomplete="off"
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
                        class="w-full rounded-lg border-slate-200 focus:border-[#ca0251] focus:ring-[#ca0251] text-sm py-1.5 px-3 @error('move_in_date') border-red-500 @enderror"
                        required>
                    @error('move_in_date')
                        <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status - REMOVED hidden field -->
                <!-- Status is now preserved from database in controller -->

            </div>

        </div>

        <!-- Footer - Compact -->
        <div class="border-t border-slate-200 px-5 py-3 flex justify-end gap-2.5">
            <a href="{{ route('landlord.properties.show', $tenant->property_id) }}"
               class="px-4 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-[#ca0251]/10 hover:text-[#ca0251] hover:border-[#ca0251] transition text-sm">
                Cancel
            </a>

            <button
                type="submit"
                class="bg-[#ca0251] hover:bg-[#a80244] text-white px-5 py-1.5 rounded-lg transition flex items-center gap-2 text-sm">
                <x-heroicon-o-check class="w-4 h-4"/>
                Update Tenant
            </button>
        </div>

    </form>

</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // =====================
        // IMPROVED MONTHLY RENT INPUT HANDLING
        // =====================
        
        const rentInput = document.getElementById('monthlyRent');
        if (rentInput) {
            /**
             * Format number with thousand separators
             * @param {string|number} value - The value to format
             * @returns {string} Formatted string with commas
             */
            function formatNumberWithCommas(value) {
                if (!value && value !== 0) return '';
                // Remove all non-numeric characters first
                const numericStr = String(value).replace(/[^0-9]/g, '');
                if (numericStr === '') return '';
                const num = parseInt(numericStr, 10);
                if (isNaN(num)) return '';
                return num.toLocaleString('en-US');
            }

            /**
             * Extract raw number from formatted string
             * @param {string} value - The formatted string
             * @returns {number} Raw number value
             */
            function getRawNumber(value) {
                if (!value) return 0;
                const numericStr = String(value).replace(/[^0-9]/g, '');
                return parseInt(numericStr, 10) || 0;
            }

            /**
             * Handle input event - format as user types
             */
            function handleRentInput(e) {
                const input = e.target;
                const selectionStart = input.selectionStart;
                const rawValue = getRawNumber(input.value);
                const formattedValue = formatNumberWithCommas(rawValue);
                
                // Store raw value as data attribute for form submission
                input.dataset.rawValue = rawValue;
                
                // Only update if the value has changed to prevent cursor jumping
                if (input.value !== formattedValue) {
                    input.value = formattedValue;
                    // Restore cursor position
                    const newPosition = Math.min(selectionStart, formattedValue.length);
                    input.setSelectionRange(newPosition, newPosition);
                }
            }

            /**
             * Handle blur event - ensure proper formatting
             */
            function handleRentBlur(e) {
                const input = e.target;
                const rawValue = getRawNumber(input.value);
                const formattedValue = formatNumberWithCommas(rawValue);
                
                input.dataset.rawValue = rawValue;
                if (formattedValue) {
                    input.value = formattedValue;
                } else {
                    input.value = '';
                }
            }

            /**
             * Handle focus event - select all text for easy editing
             */
            function handleRentFocus(e) {
                e.target.select();
            }

            /**
             * Handle keydown event - prevent non-numeric characters
             */
            function handleRentKeydown(e) {
                // Allow: backspace, delete, tab, escape, enter, home, end, left, right
                const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'Home', 'End', 'ArrowLeft', 'ArrowRight'];
                if (allowedKeys.includes(e.key)) {
                    return;
                }
                
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                if (e.ctrlKey && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) {
                    return;
                }
                
                // Prevent letter keys, space, symbols, currency symbols
                if (!/^\d$/.test(e.key)) {
                    e.preventDefault();
                }
            }

            /**
             * Handle paste event - clean pasted content
             */
            function handleRentPaste(e) {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                if (pastedData) {
                    const cleanData = pastedData.replace(/[^0-9]/g, '');
                    if (cleanData) {
                        const rawValue = parseInt(cleanData, 10) || 0;
                        const formattedValue = formatNumberWithCommas(rawValue);
                        rentInput.value = formattedValue;
                        rentInput.dataset.rawValue = rawValue;
                    }
                }
            }

            // Attach event listeners
            rentInput.addEventListener('input', handleRentInput);
            rentInput.addEventListener('blur', handleRentBlur);
            rentInput.addEventListener('focus', handleRentFocus);
            rentInput.addEventListener('keydown', handleRentKeydown);
            rentInput.addEventListener('paste', handleRentPaste);

            // Initialize with proper formatting
            const initialRawValue = getRawNumber(rentInput.value);
            if (initialRawValue > 0) {
                rentInput.value = formatNumberWithCommas(initialRawValue);
                rentInput.dataset.rawValue = initialRawValue;
            }
        }

        // =====================
        // FORM SUBMISSION HANDLER - Remove commas before submit
        // =====================
        document.getElementById('editTenantForm')?.addEventListener('submit', function(e) {
            const rentInput = document.getElementById('monthlyRent');
            if (rentInput) {
                // Get raw value from data attribute or parse from input
                let rawValue = rentInput.dataset.rawValue;
                if (!rawValue || rawValue === '') {
                    rawValue = getRawNumber(rentInput.value);
                }
                // Set the input value to raw number (no commas) before submission
                rentInput.value = rawValue;
            }
        });

        // Helper function for getRawNumber (exposed globally for the submit handler)
        window.getRawNumber = function(value) {
            if (!value) return 0;
            const numericStr = String(value).replace(/[^0-9]/g, '');
            return parseInt(numericStr, 10) || 0;
        };
    });
</script>

@endsection