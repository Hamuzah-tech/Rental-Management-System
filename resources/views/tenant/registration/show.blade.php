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

            <!-- Form -->
            <form method="POST"
                  action="{{ route('tenant.registration.store', $property->registration_token) }}"
                  id="registrationForm">

                @csrf

                <div class="p-6 space-y-4">

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 p-3">
                            <ul class="list-disc list-inside text-red-600 text-xs space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Name & Email - Side by Side -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] px-3 py-1.5 text-sm bg-white text-[#111827]">

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
                                value="{{ old('email') }}"
                                required
                                class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] px-3 py-1.5 text-sm bg-white text-[#111827]">

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
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] px-3 py-1.5 text-sm bg-white text-[#111827]">

                        @error('phone')
                            <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
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
                                        MK {{ number_format($property->monthly_rent ?? 0, 2) }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-[#6B7280] mt-0.5">Using the default rent set by the landlord</p>
                                <input type="hidden" name="monthly_rent" id="defaultRentInput" value="{{ $property->monthly_rent ?? 0 }}">
                            </div>

                            <!-- Custom Rent Input -->
                            <div id="customRentContainer" class="hidden">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-medium text-[#6B7280]">MK</span>
                                    <input type="number" 
                                           step="0.01" 
                                           name="custom_monthly_rent" 
                                           id="customRentInput"
                                           value="{{ old('custom_monthly_rent') }}"
                                           class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] pl-10 pr-3 py-1.5 text-sm bg-white text-[#111827]"
                                           placeholder="Enter your specific rent amount">
                                </div>
                                <p class="text-[10px] text-[#6B7280] mt-0.5">Enter a specific rent amount if you have a different agreement</p>
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
                            value="{{ old('move_in_date', date('Y-m-d')) }}"
                            required
                            class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] px-3 py-1.5 text-sm bg-white text-[#111827]">

                        @error('move_in_date')
                            <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Footer -->
                <div class="border-t border-[#E5E7EB] bg-[#F8FAFC] px-6 py-3">
                    <button
                        type="submit"
                        class="w-full bg-[#0F172A] hover:bg-[#1a2a4a] text-white py-2 rounded-lg font-medium text-sm transition">

                        Register

                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

<script>
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
        } else {
            defaultContainer.classList.add('hidden');
            customContainer.classList.remove('hidden');
            customBtn.className = 'flex-1 py-1.5 px-3 text-xs font-medium rounded-lg border transition bg-[#0F172A] text-white border-[#0F172A] hover:bg-[#1a2a4a]';
            defaultBtn.className = 'flex-1 py-1.5 px-3 text-xs font-medium rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] transition';
            defaultInput.disabled = true;
            customInput.disabled = false;
            defaultInput.value = '';
            customInput.setAttribute('required', 'required');
        }
    }

    // Handle form submission - FIXED
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
            // Custom rent selected - use custom value
            const customValue = customInput.value;
            if (customValue) {
                // Create or update the monthly_rent field with custom value
                defaultInput.disabled = false;
                defaultInput.value = customValue;
            }
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        @if(old('custom_monthly_rent'))
            toggleRent('custom');
            document.getElementById('customRentInput').value = '{{ old('custom_monthly_rent') }}';
        @endif
    });
</script>

@endsection