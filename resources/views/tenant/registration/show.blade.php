<!-- resources/views/tenant/registration/show.blade.php -->

@extends('layouts.guest')

@section('title', 'Tenant Registration')

@section('content')
<div class="min-h-screen bg-slate-100 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-200">
                <h1 class="text-2xl font-bold text-slate-800">
                    Register for {{ $property->name }}
                </h1>
                <p class="text-slate-500 mt-1">
                    Fill in your details to register as a tenant.
                </p>
                <p class="text-sm text-slate-400 mt-1">
                    Available slots: {{ $property->availableSlots() }}
                </p>
            </div>

            <!-- Error Display -->
            @if($errors->any())
                <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="text-red-600 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.registration.store', $property->registration_token) }}" class="p-8 space-y-5" id="registrationForm">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 @error('name') border-red-500 @enderror"
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
                           class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="phone"
                           value="{{ old('phone') }}"
                           class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 @error('phone') border-red-500 @enderror"
                           placeholder="0977123456"
                           pattern="[0-9]{10,15}"
                           required>
                    <div id="phoneStatus" class="text-xs mt-1"></div>
                    <p class="text-xs text-slate-400 mt-1">
                        You can use the same phone number if you're moving from another property.
                    </p>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Monthly Rent - Using Alpine.js -->
                <div x-data="{ rentOption: '{{ old('rent_option', 'default') }}' }">
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Monthly Rent (MK) <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="space-y-3">
                        <!-- Rent Option: Default / Custom Toggle -->
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" 
                                       name="rent_option" 
                                       value="default" 
                                       x-model="rentOption"
                                       class="w-4 h-4 text-slate-600 focus:ring-slate-500">
                                <span class="text-sm text-slate-700">Use Default Rent</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" 
                                       name="rent_option" 
                                       value="custom" 
                                       x-model="rentOption"
                                       class="w-4 h-4 text-slate-600 focus:ring-slate-500">
                                <span class="text-sm text-slate-700">Enter Custom Rent</span>
                            </label>
                        </div>

                        <!-- Default Rent Display -->
                        <div x-show="rentOption === 'default'" 
                             class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Property Default Rent</span>
                                <span class="text-lg font-bold text-slate-800">
                                    MK {{ number_format($property->monthly_rent ?? 0, 2) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">
                                This is the standard rent amount set by the landlord.
                            </p>
                            <input type="hidden" 
                                   name="monthly_rent" 
                                   value="{{ $property->monthly_rent ?? 0 }}">
                        </div>

                        <!-- Custom Rent Input -->
                        <div x-show="rentOption === 'custom'" 
                             class="bg-blue-50 rounded-xl p-4 border border-blue-300">
                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Enter Custom Rent Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-medium">MK</span>
                                <input
                                    type="number"
                                    name="custom_monthly_rent"
                                    id="customMonthlyRent"
                                    value="{{ old('custom_monthly_rent') }}"
                                    class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 text-sm pl-12 @error('custom_monthly_rent') border-red-500 @enderror"
                                    step="0.01"
                                    min="0"
                                    placeholder="e.g. 85000"
                                    :required="rentOption === 'custom'">
                            </div>
                            <p class="text-xs text-slate-500 mt-2">
                                Enter your specific rent amount if you have a different agreement.
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                Example: Single room, different rate, or special arrangement.
                            </p>
                            @error('custom_monthly_rent')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
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
                           class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400 @error('move_in_date') border-red-500 @enderror"
                           required>
                    @error('move_in_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl transition">
                    Register Now
                </button>
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
                            phoneStatus.innerHTML = '<span class="text-red-500">' + data.message + '</span>';
                            this.classList.add('border-red-500');
                        } else {
                            phoneStatus.innerHTML = '<span class="text-green-500">' + data.message + '</span>';
                            this.classList.remove('border-red-500');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        }

        // Handle form submission for custom rent
        const form = document.getElementById('registrationForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Alpine.js will handle the rent option
                console.log('Form submitted');
            });
        }
    });
</script>
@endpush
@endsection