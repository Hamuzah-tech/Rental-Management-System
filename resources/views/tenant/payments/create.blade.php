<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Record Payment · Tenant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">

    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('tenant.payments.index') }}"
                   class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            </div>

            <!-- Page Header -->
            <h1 class="text-xl font-bold text-slate-800">Record Payment</h1>
            <p class="text-sm text-slate-500">Submit your rent payment with proof</p>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-red-600 text-xs list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-xs">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Card -->
            <div class="mt-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                <form method="POST"
                      action="{{ route('tenant.payments.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-3.5"
                      id="paymentForm">

                    @csrf

                    <!-- Tenant Code -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">
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
                        <label class="block text-xs font-medium text-slate-700 mb-1">
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
                        <label class="block text-xs font-medium text-slate-700 mb-1">
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
                        <label class="block text-xs font-medium text-slate-700 mb-1">
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
                        <p class="text-xs text-slate-400 mt-0.5">Select multiple months if paying in advance</p>
                        <input type="hidden" name="months" id="monthsHidden" value="{{ old('month_count', 1) }}">
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">
                            Amount Paid (MK) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-medium text-slate-500">MK</span>
                            <input type="number" 
                                   step="0.01" 
                                   name="amount" 
                                   value="{{ old('amount') }}"
                                   required
                                   class="w-full rounded-lg border border-slate-200 bg-white pl-11 pr-3 py-1.5 text-sm text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition @error('amount') border-red-500 @enderror" />
                        </div>
                        @error('amount')
                            <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Screenshot -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">
                            Payment Screenshot <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               name="screenshot" 
                               required
                               accept="image/*,.pdf"
                               class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-800 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1 file:text-xs file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 transition @error('screenshot') border-red-500 @enderror" />
                        @error('screenshot')
                            <p class="text-red-500 text-xs mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full rounded-lg bg-[#0F172A] px-6 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1e293b] focus:outline-none focus:ring-2 focus:ring-[#0F172A] focus:ring-offset-2 mt-1">
                        Submit Payment
                    </button>

                </form>
            </div>

        </div>
    </div>

    <!-- Success Modal -->
    @if(session('success'))
    <div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl transform transition-all scale-100">
            <div class="flex flex-col items-center text-center">
                <div class="relative mb-6">
                    <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" 
                                  d="M5 13l4 4L19 7"
                                  class="tick-path" />
                        </svg>
                    </div>
                    <div class="absolute inset-0 rounded-full border-4 border-green-400 animate-ping opacity-75"></div>
                </div>

                <h2 class="text-2xl font-bold text-slate-800 mb-2">Payment Recorded Successfully</h2>
                <p class="text-slate-500 text-sm mb-6">
                    @php
                        $monthCount = old('month_count', 1);
                    @endphp
                    @if($monthCount > 1)
                        Your payment for {{ $monthCount }} months has been recorded and is pending approval.
                    @else
                        Your payment has been recorded and is pending approval.
                    @endif
                </p>

                <a href="{{ route('tenant.payments.index') }}"
                   class="w-full inline-flex items-center justify-center rounded-lg bg-[#0F172A] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1e293b] focus:outline-none focus:ring-2 focus:ring-[#0F172A] focus:ring-offset-2">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
    @endif

    <style>
        @keyframes drawTick {
            0% { stroke-dashoffset: 50; }
            100% { stroke-dashoffset: 0; }
        }

        .tick-path {
            stroke-dasharray: 50;
            stroke-dashoffset: 50;
            animation: drawTick 0.6s ease-in-out forwards;
            animation-delay: 0.2s;
        }

        @keyframes ping {
            0% { transform: scale(1); opacity: 0.75; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .animate-ping {
            animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        #successModal > div {
            animation: modalIn 0.4s ease-out;
        }

        @keyframes modalIn {
            0% { opacity: 0; transform: scale(0.9) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>

    {{-- Script - Always available --}}
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

        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('successModal');
            if (modal) {
                setTimeout(function() {
                    modal.style.opacity = '0';
                    modal.style.transition = 'opacity 0.5s ease';
                    setTimeout(function() {
                        modal.style.display = 'none';
                    }, 500);
                }, 5000);

                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.opacity = '0';
                        this.style.transition = 'opacity 0.3s ease';
                        setTimeout(function() {
                            this.style.display = 'none';
                        }, 300);
                    }
                });
            }
        });
    </script>

</body>
</html>