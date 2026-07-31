@extends('layouts.guest')

@section('content')

<div class="min-h-screen bg-[#F8FAFC] py-8 px-4">

    <div class="max-w-3xl mx-auto">

        <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-[#E5E7EB]">
                <div>
                    <h1 class="text-lg font-bold text-[#111827]">
                        Tenant Registration
                    </h1>
                    <p class="text-xs text-[#6B7280] mt-0.5">
                        Complete the form below to register as a tenant.
                    </p>
                </div>
            </div>

            <!-- Property -->
            <div class="px-6 py-3 bg-[#F8FAFC] border-b border-[#E5E7EB]">
                <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm">
                    <span class="text-[#6B7280]">Property:</span>
                    <span class="font-medium text-[#111827]">{{ $property->name }}</span>
                    <span class="text-[#6B7280]">|</span>
                    <span class="text-[#6B7280]">Rent:</span>
                    <span class="font-medium text-[#111827]">MK {{ number_format($property->monthly_rent ?? 0) }}</span>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mx-6 mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Form -->
            <form method="POST"
                  action="{{ route('tenant.registration.store', $property->registration_token) }}"
                  id="registrationForm"
                  novalidate>

                @csrf

                <div class="p-6 space-y-4">

                    <!-- Name & Email - Side by Side -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                required
                                maxlength="255"
                                class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] px-3 py-1.5 text-sm bg-white text-[#111827] @error('name') border-red-500 @enderror">

                            @error('name')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                required
                                maxlength="255"
                                class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] px-3 py-1.5 text-sm bg-white text-[#111827] @error('email') border-red-500 @enderror">

                            @error('email')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-xs font-medium text-[#374151] mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            value="{{ old('phone') }}"
                            maxlength="15"
                            required
                            class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] px-3 py-1.5 text-sm bg-white text-[#111827] @error('phone') border-red-500 @enderror">

                        @error('phone')
                            <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                        <p class="text-[10px] text-[#6B7280] mt-0.5">Enter a valid Malawi phone number</p>
                    </div>

                    <!-- Monthly Rent with Default/Custom Toggle -->
                    <div>
                        <label class="block text-xs font-medium text-[#374151] mb-1">
                            Monthly Rent (MK) <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="space-y-2">
                            <!-- Toggle Buttons -->
                            <div class="flex gap-2">
                                <button type="button" 
                                        id="defaultRentBtn"
                                        onclick="toggleRent('default')"
                                        class="flex-1 py-1.5 px-3 text-xs font-medium rounded-lg border transition bg-[#0F172A] text-white border-[#0F172A] hover:bg-[#1a2a4a]">
                                    Default Rent
                                </button>
                                <button type="button" 
                                        id="customRentBtn"
                                        onclick="toggleRent('custom')"
                                        class="flex-1 py-1.5 px-3 text-xs font-medium rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] transition">
                                    Custom Amount
                                </button>
                            </div>

                            <!-- Default Rent Display -->
                            <div id="defaultRentContainer" class="bg-[#F8FAFC] rounded-lg p-3 border border-[#E5E7EB]">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-[#6B7280]">Property Default Rent</span>
                                    <span class="text-base font-bold text-[#111827]">
                                        MK {{ number_format($property->monthly_rent ?? 0) }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-[#6B7280] mt-0.5">Using the default rent set by the landlord</p>
                                <input type="hidden" name="monthly_rent" id="defaultRentInput" value="{{ $property->monthly_rent ?? 0 }}">
                            </div>

                            <!-- Custom Rent Input with Currency Formatting -->
                            <div id="customRentContainer" class="hidden">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-medium text-[#6B7280]">MK</span>
                                    <input type="text" 
                                           name="custom_monthly_rent" 
                                           id="customRentInput"
                                           value="{{ old('custom_monthly_rent') ? number_format((float) str_replace(',', '', old('custom_monthly_rent')), 0, '.', ',') : '' }}"
                                           class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] pl-10 pr-3 py-1.5 text-sm bg-white text-[#111827] currency-input @error('custom_monthly_rent') border-red-500 @enderror"

                                           autocomplete="off">
                                </div>
                                <p class="text-[10px] text-[#6B7280] mt-0.5">Enter a specific rent amount if you have a different agreement e.g single room</p>
                            </div>
                        </div>
                        @error('monthly_rent')
                            <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                        @error('custom_monthly_rent')
                            <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Move In Date -->
                    <div>
                        <label class="block text-xs font-medium text-[#374151] mb-1">
                            Move In Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            name="move_in_date"
                            id="move_in_date"
                            value="{{ old('move_in_date', date('Y-m-d')) }}"
                            required
                            class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] px-3 py-1.5 text-sm bg-white text-[#111827] @error('move_in_date') border-red-500 @enderror">

                        @error('move_in_date')
                            <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Footer -->
                <div class="border-t border-[#E5E7EB] bg-[#F8FAFC] px-6 py-3">
                    <button
                        type="submit"
                        id="submitBtn"
                        class="w-full bg-[#0F172A] hover:bg-[#1a2a4a] text-white py-2 rounded-lg font-medium text-sm transition">
                        Register
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

<script>
    // Currency formatting function
    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        
        if (value === '') {
            input.value = '';
            return;
        }
        
        let number = parseInt(value);
        if (!isNaN(number)) {
            input.value = number.toLocaleString('en-US');
        }
    }

    // Handle paste events to clean up pasted values
    function handlePaste(e) {
        e.preventDefault();
        let pastedText = (e.clipboardData || window.clipboardData).getData('text');
        let cleaned = pastedText.replace(/\D/g, '');
        if (cleaned) {
            let number = parseInt(cleaned);
            if (!isNaN(number)) {
                e.target.value = number.toLocaleString('en-US');
            }
        }
    }

    // Toggle between default and custom rent
    function toggleRent(type) {
        const defaultContainer = document.getElementById('defaultRentContainer');
        const customContainer = document.getElementById('customRentContainer');
        const defaultBtn = document.getElementById('defaultRentBtn');
        const customBtn = document.getElementById('customRentBtn');
        const defaultInput = document.getElementById('defaultRentInput');
        const customInput = document.getElementById('customRentInput');

        if (type === 'default') {
            defaultContainer.classList.remove('hidden');
            customContainer.classList.add('hidden');
            defaultBtn.className = 'flex-1 py-1.5 px-3 text-xs font-medium rounded-lg border transition bg-[#0F172A] text-white border-[#0F172A] hover:bg-[#1a2a4a]';
            customBtn.className = 'flex-1 py-1.5 px-3 text-xs font-medium rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] transition';
            defaultInput.disabled = false;
            customInput.disabled = true;
            customInput.value = '';
            customInput.removeAttribute('required');
            customInput.classList.remove('border-red-500');
        } else {
            defaultContainer.classList.add('hidden');
            customContainer.classList.remove('hidden');
            customBtn.className = 'flex-1 py-1.5 px-3 text-xs font-medium rounded-lg border transition bg-[#0F172A] text-white border-[#0F172A] hover:bg-[#1a2a4a]';
            defaultBtn.className = 'flex-1 py-1.5 px-3 text-xs font-medium rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] transition';
            defaultInput.disabled = true;
            customInput.disabled = false;
            defaultInput.value = '';
            customInput.setAttribute('required', 'required');
            customInput.focus();
        }
    }

    // Phone input validation - only allow digits and + sign
    document.getElementById('phone')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9+]/g, '');
        if (this.value.length > 15) {
            this.value = this.value.slice(0, 15);
        }
    });

    // Handle form submission - ensure monthly_rent is set
    document.getElementById('registrationForm')?.addEventListener('submit', function(e) {
        const defaultContainer = document.getElementById('defaultRentContainer');
        const customInput = document.getElementById('customRentInput');
        const defaultInput = document.getElementById('defaultRentInput');
        
        // Always ensure the hidden monthly_rent field has the correct value
        if (!defaultContainer.classList.contains('hidden')) {
            // Default rent selected
            defaultInput.disabled = false;
            defaultInput.value = defaultInput.value || '{{ $property->monthly_rent ?? 0 }}';
        } else {
            // Custom rent selected - use custom value (remove commas for backend)
            let customValue = customInput.value.replace(/,/g, '');
            if (customValue && parseInt(customValue) > 0) {
                defaultInput.disabled = false;
                defaultInput.value = customValue;
            } else {
                // If custom rent is invalid, prevent submission
                e.preventDefault();
                customInput.classList.add('border-red-500');
                customInput.focus();
                
                // Show error message if not already shown
                let errorEl = document.getElementById('customRentError');
                if (!errorEl) {
                    errorEl = document.createElement('p');
                    errorEl.id = 'customRentError';
                    errorEl.className = 'text-red-500 text-xs mt-0.5';
                    errorEl.textContent = 'Please enter a valid rent amount.';
                    customInput.parentElement.parentElement.appendChild(errorEl);
                }
                return;
            }
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set up currency formatting on the custom input
        const customInput = document.getElementById('customRentInput');
        if (customInput) {
            customInput.addEventListener('input', function() {
                formatCurrency(this);
                // Remove error message when user types
                const errorEl = document.getElementById('customRentError');
                if (errorEl) errorEl.remove();
                this.classList.remove('border-red-500');
            });
            customInput.addEventListener('paste', handlePaste);
            
            customInput.addEventListener('blur', function() {
                if (this.value) {
                    let value = this.value.replace(/\D/g, '');
                    if (value) {
                        let number = parseInt(value);
                        if (!isNaN(number) && number > 0) {
                            this.value = number.toLocaleString('en-US');
                            this.classList.remove('border-red-500');
                        }
                    }
                }
            });
        }

        @if(old('custom_monthly_rent'))
            toggleRent('custom');
            const customInputField = document.getElementById('customRentInput');
            if (customInputField) {
                const value = '{{ old('custom_monthly_rent') }}';
                if (value) {
                    const number = parseInt(value);
                    if (!isNaN(number) && number > 0) {
                        customInputField.value = number.toLocaleString('en-US');
                        customInputField.classList.remove('border-red-500');
                    }
                }
            }
        @endif

        // If there are validation errors, scroll to the first error
        const firstError = document.querySelector('.border-red-500');
        if (firstError) {
            firstError.focus();
            setTimeout(() => {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    });
</script>

<style>
    .currency-input {
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    .currency-input::placeholder {
        font-weight: normal;
        letter-spacing: normal;
        color: #9CA3AF;
    }

    .currency-input:focus {
        border-color: #0F172A;
        ring: 2px solid rgba(15, 23, 42, 0.1);
    }

    .hidden {
        display: none !important;
    }

    .transition {
        transition: all 0.2s ease-in-out;
    }

    .border-red-500 {
        border-color: #EF4444 !important;
    }

    .border-red-500:focus {
        ring-color: #EF4444 !important;
    }
</style>

@endsection