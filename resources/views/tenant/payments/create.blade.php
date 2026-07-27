<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Record Payment · Tenant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 h-screen overflow-hidden flex items-center justify-center">

    <div class="w-full max-w-5xl px-4 py-4 h-screen flex flex-col justify-center">
        
        <!-- Top Bar -->
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant.payments.index') }}"
                   class="inline-flex items-center gap-1.5 text-slate-500 hover:text-slate-800 transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
                <span class="text-slate-300">|</span>
                <h1 class="text-base font-bold text-slate-800">Record Payment</h1>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-3 p-2.5 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
                    <p class="text-green-600 text-xs">
                        @php
                            $monthCount = session('payment_month_count', 1);
                        @endphp
                        @if($monthCount > 1)
                            • {{ $monthCount }} months paid
                        @endif
                    </p>
                </div>
                <button onclick="this.parentElement.style.display='none'" class="text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-3 p-2.5 bg-red-50 border border-red-200 rounded-lg">
                <ul class="text-red-600 text-xs list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-3 p-2.5 bg-red-50 border border-red-200 rounded-lg text-red-600 text-xs">
                {{ session('error') }}
            </div>
        @endif

        <!-- Card -->
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm flex-1 max-h-[calc(100vh-140px)] overflow-y-auto">
            
            <form method="POST"
                  action="{{ route('tenant.payments.store') }}"
                  enctype="multipart/form-data"
                  class="h-full"
                  id="paymentForm">
                @csrf

                <div class="grid grid-cols-2 gap-4 h-full">
                    
                    <!-- Left Column -->
                    <div class="space-y-3">
                        <!-- Tenant Code -->
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-0.5">
                                Tenant Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="tenant_code" 
                                   value="{{ old('tenant_code') }}"
                                   required
                                   class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition @error('tenant_code') border-red-500 @enderror" />
                            @error('tenant_code')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tenant Name -->
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-0.5">
                                Tenant Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="tenant_name" 
                                   value="{{ old('tenant_name') }}"
                                   required
                                   class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition @error('tenant_name') border-red-500 @enderror" />
                            @error('tenant_name')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Month -->
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-0.5">
                                Payment Month <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <select name="payment_month" 
                                        id="paymentMonth"
                                        required
                                        class="w-full rounded-lg border border-slate-200 bg-white pl-9 pr-8 py-1.5 text-sm text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition appearance-none @error('payment_month') border-red-500 @enderror">
                                    <option value="">Select month</option>
                                    @php
                                        $startDate = \Carbon\Carbon::now()->subMonths(6);
                                        $endDate = \Carbon\Carbon::now()->addMonths(18);
                                        for ($date = clone $startDate; $date <= $endDate; $date->addMonth()) {
                                            $value = $date->format('Y-m');
                                            $label = $date->format('F Y');
                                        @endphp
                                        <option value="{{ $value }}" {{ old('payment_month') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @php } @endphp
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('payment_month')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Number of Months -->
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-0.5">
                                Number of Months
                            </label>
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        onclick="adjustMonths(-1)"
                                        class="w-7 h-7 rounded-lg border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 transition text-sm">
                                    −
                                </button>
                                <input type="number" 
                                       name="month_count" 
                                       id="monthCount"
                                       value="{{ old('month_count', 1) }}"
                                       min="1"
                                       max="12"
                                       class="w-14 text-center rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                       readonly>
                                <button type="button" 
                                        onclick="adjustMonths(1)"
                                        class="w-7 h-7 rounded-lg border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 transition text-sm">
                                    +
                                </button>
                                <span class="text-xs text-slate-500">month(s)</span>
                            </div>
                            <input type="hidden" name="months" id="monthsHidden" value="{{ old('month_count', 1) }}">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-3">
                        <!-- Amount -->
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-0.5">
                                Amount Paid (MK) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-medium text-slate-500">MK</span>
                                <input type="text" 
                                       id="amountInput"
                                       name="amount_display"
                                       value="{{ old('amount') ? number_format(old('amount'), 0, '.', ',') : '' }}"
                                       class="w-full rounded-lg border border-slate-200 bg-white pl-11 pr-3 py-1.5 text-sm text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition @error('amount') border-red-500 @enderror"
                                       autocomplete="off" />
                                <input type="hidden" name="amount" id="amountHidden" value="{{ old('amount') }}" />
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Screenshot -->
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-0.5">
                                Payment Screenshot <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       name="screenshot" 
                                       required
                                       accept="image/*,.pdf"
                                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-800 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1 file:text-xs file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 transition @error('screenshot') border-red-500 @enderror" />
                            </div>
                            @error('screenshot')
                                <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full rounded-lg bg-[#0F172A] px-6 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1e293b] focus:outline-none focus:ring-2 focus:ring-[#0F172A] focus:ring-offset-2">
                                Submit Payment
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function adjustMonths(delta) {
            var input = document.getElementById('monthCount');
            if (!input) {
                console.error('monthCount input not found');
                return;
            }
            var value = parseInt(input.value) || 1;
            value = value + delta;
            if (value < 1) value = 1;
            if (value > 12) value = 12;
            input.value = value;
            
            var hidden = document.getElementById('monthsHidden');
            if (hidden) {
                hidden.value = value;
            }
        }

        // Amount formatting with thousands separators
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amountInput');
            const amountHidden = document.getElementById('amountHidden');

            if (amountInput) {
                // Format initial value
                if (amountInput.value) {
                    const rawValue = amountInput.value.replace(/,/g, '');
                    if (!isNaN(rawValue) && rawValue !== '') {
                        amountInput.value = new Intl.NumberFormat('en-US').format(parseInt(rawValue));
                        amountHidden.value = rawValue;
                    }
                }

                // Handle input events
                amountInput.addEventListener('input', function(e) {
                    // Remove non-numeric characters
                    let rawValue = this.value.replace(/,/g, '').replace(/[^0-9]/g, '');
                    
                    if (rawValue === '') {
                        this.value = '';
                        amountHidden.value = '';
                        return;
                    }

                    // Parse as integer
                    const numericValue = parseInt(rawValue);
                    if (!isNaN(numericValue) && numericValue >= 0) {
                        // Format with thousands separators
                        this.value = new Intl.NumberFormat('en-US').format(numericValue);
                        amountHidden.value = numericValue.toString();
                    }
                });

                // Handle blur - ensure proper formatting
                amountInput.addEventListener('blur', function() {
                    const rawValue = this.value.replace(/,/g, '');
                    if (rawValue !== '' && !isNaN(rawValue)) {
                        const numericValue = parseInt(rawValue);
                        this.value = new Intl.NumberFormat('en-US').format(numericValue);
                        amountHidden.value = numericValue.toString();
                    }
                });

                // Handle focus - show raw number for editing
                amountInput.addEventListener('focus', function() {
                    const rawValue = this.value.replace(/,/g, '');
                    if (rawValue !== '' && !isNaN(rawValue)) {
                        this.value = rawValue;
                    }
                });

                // Prevent non-numeric input
                amountInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        return;
                    }
                    // Allow only digits
                    if (!/^\d$/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && 
                        e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' && e.key !== 'Tab') {
                        e.preventDefault();
                    }
                });

                // Paste prevention - only allow numbers
                amountInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text');
                    const numbers = pasted.replace(/[^0-9]/g, '');
                    if (numbers) {
                        this.value = new Intl.NumberFormat('en-US').format(parseInt(numbers));
                        amountHidden.value = numbers;
                    }
                });
            }

            // Auto-hide success message after 5 seconds
            var successMessage = document.querySelector('.bg-green-50');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.transition = 'opacity 0.5s ease';
                    successMessage.style.opacity = '0';
                    setTimeout(function() {
                        successMessage.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>

    <style>
        /* Remove up/down arrows from number input */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Custom scrollbar for the form */
        .overflow-y-auto::-webkit-scrollbar {
            width: 4px;
        }
        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

</body>
</html>