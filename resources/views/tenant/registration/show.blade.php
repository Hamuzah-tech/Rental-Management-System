<!-- resources/views/tenant/registration/show.blade.php -->

@extends('layouts.guest')

@section('title', 'Tenant Registration')

@section('content')
<div class="min-h-screen bg-slate-100 py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            
            <!-- Header - Compact -->
            <div class="px-6 py-4 border-b border-slate-200">
                <h1 class="text-xl font-bold text-slate-800">
                    Register for {{ $property->name }}
                </h1>
                <div class="flex items-center gap-3 mt-1">
                    <p class="text-sm text-slate-500">
                        Fill in your details to register
                    </p>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                        {{ $property->availableSlots() }} slots left
                    </span>
                </div>
            </div>

            <!-- Error Display -->
            @if($errors->any())
                <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="text-red-600 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.registration.store', $property->registration_token) }}" class="p-6 space-y-4" id="registrationForm">
                @csrf

                <!-- Two Column Layout for Name & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="phone"
                           value="{{ old('phone') }}"
                           class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm @error('phone') border-red-500 @enderror"
                           pattern="[0-9]{10,15}"
                           required>
                    <div id="phoneStatus" class="text-xs mt-1"></div>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Monthly Rent - Using Alpine.js -->
                <div x-data="{ 
                    rentOption: '{{ old('rent_option', 'default') }}',
                    customRent: '{{ old('custom_monthly_rent') }}',
                    
                    formatNumber(value) {
                        if (!value) return '';
                        // Remove non-numeric characters
                        let num = value.toString().replace(/,/g, '').replace(/[^\d]/g, '');
                        if (num === '') return '';
                        // Format with commas
                        return Number(num).toLocaleString('en-US');
                    },
                    
                    updateCustomRent(e) {
                        const input = e.target;
                        const cursorPosition = input.selectionStart;
                        const oldLength = input.value.length;
                        
                        // Get raw number
                        let raw = input.value.replace(/,/g, '').replace(/[^\d]/g, '');
                        
                        if (raw === '') {
                            this.customRent = '';
                            return;
                        }
                        
                        // Format with commas
                        const formatted = Number(raw).toLocaleString('en-US');
                        this.customRent = formatted;
                        
                        // Store raw value for submission
                        this.$el.dataset.rawValue = raw;
                    }
                }">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Monthly Rent (MK) <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="space-y-2">
                        <!-- Rent Option: Default / Custom Toggle -->
                        <div class="flex gap-4 bg-slate-50 rounded-lg p-1.5 border border-slate-200">
                            <label class="flex-1 flex items-center justify-center gap-2 cursor-pointer px-3 py-1.5 rounded-md transition"
                                  :class="rentOption === 'default' ? 'bg-white shadow-sm' : ''">
                                <input type="radio" 
                                       name="rent_option" 
                                       value="default" 
                                       x-model="rentOption"
                                       class="hidden">
                                <span class="text-sm font-medium text-slate-700">Default</span>
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 cursor-pointer px-3 py-1.5 rounded-md transition"
                                  :class="rentOption === 'custom' ? 'bg-white shadow-sm' : ''">
                                <input type="radio" 
                                       name="rent_option" 
                                       value="custom" 
                                       x-model="rentOption"
                                       class="hidden">
                                <span class="text-sm font-medium text-slate-700">Custom</span>
                            </label>
                        </div>

                        <!-- Default Rent Display -->
                        <div x-show="rentOption === 'default'" 
                             x-transition:enter.duration.200ms
                             class="bg-slate-50 rounded-lg p-3 border border-slate-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Default Rent</span>
                                <span class="text-base font-bold text-slate-800">
                                    MK {{ number_format($property->monthly_rent ?? 0, 2) }}
                                </span>
                            </div>
                            <input type="hidden" 
                                   name="monthly_rent" 
                                   value="{{ $property->monthly_rent ?? 0 }}">
                        </div>

                        <!-- Custom Rent Input -->
                        <div x-show="rentOption === 'custom'" 
                             x-transition:enter.duration.200ms
                             class="bg-blue-50 rounded-lg p-3 border border-blue-300">
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-medium text-sm">MK</span>
                                <input
                                    type="text"
                                    name="custom_monthly_rent_display"
                                    id="customMonthlyRent"
                                    x-model="customRent"
                                    @input="updateCustomRent"
                                    class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm pl-12 @error('custom_monthly_rent') border-red-500 @enderror"
                                    :required="rentOption === 'custom'"
                                    placeholder="0">
                                <input type="hidden" 
                                       name="custom_monthly_rent" 
                                       x-bind:value="customRent.replace(/,/g, '')">
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                For single room, different rate, or special arrangement.
                            </p>
                            @error('custom_monthly_rent')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Move In Date -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Move In Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="move_in_date" 
                           value="{{ old('move_in_date', date('Y-m-d')) }}"
                           class="w-full rounded-lg border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm @error('move_in_date') border-red-500 @enderror"
                           required>
                    @error('move_in_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-slate-800 hover:bg-slate-900 text-white py-2.5 rounded-lg text-sm font-medium transition mt-2">
                    Register Now
                </button>

                <p class="text-xs text-slate-400 text-center pt-1">
                    By registering, you agree to the terms and conditions.
                </p>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Phone uniqueness check
        const phoneInput = document.getElementById('phone');
        const phoneStatus = document.getElementById('phoneStatus');
        
        if (phoneInput) {
            phoneInput.addEventListener('blur', function() {
                const phone = this.value;
                
                if (phone.length >= 10) {
                    fetch('{{ route('tenant.check-phone') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            phone: phone,
                            property_id: '{{ $property->id }}'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            phoneStatus.innerHTML = '<span class="text-red-500 text-xs">' + data.message + '</span>';
                            this.classList.add('border-red-500');
                        } else {
                            phoneStatus.innerHTML = '<span class="text-green-500 text-xs">' + data.message + '</span>';
                            this.classList.remove('border-red-500');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        }
    });
</script>
@endpush
@endsection