<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment History · Tenant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F8FAFC]">

    <div class="min-h-screen px-4 py-8">
        <div class="max-w-4xl mx-auto">

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('tenant.payments.index') }}"
                   class="inline-flex items-center gap-2 text-[#6B7280] hover:text-[#ca0251] transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back</span>
                </a>
            </div>

            <!-- Header -->
            <h1 class="text-2xl font-bold text-[#111827]">Payment History</h1>
            <p class="mt-1 text-sm text-[#6B7280]">
                Check your approved, pending, and rejected payments.
            </p>

            @if(session('success'))
                <div class="mt-4 p-3 bg-[#ca0251]/10 border border-[#ca0251] rounded-lg text-[#ca0251] text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-red-600 text-xs list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ e($error) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Search Form -->
            <div class="mt-6 bg-white border border-[#E5E7EB] rounded-xl p-4">
                <form method="GET" action="{{ route('tenant.payments.history') }}" class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-end gap-3">
                    <div class="flex-1 min-w-0 sm:min-w-[200px]">
                        <label class="block text-xs font-medium text-[#374151] mb-1">Tenant Code</label>
                        <input type="text" 
                               name="tenant_code" 
                               value="{{ request('tenant_code') }}"
                               placeholder="Enter your tenant code"
                               maxlength="50"
                               class="w-full rounded-lg border-[#E5E7EB] focus:border-[#ca0251] focus:ring-[#ca0251] px-3 py-1.5 text-sm bg-white text-[#111827]">
                    </div>
                    <button type="submit" 
                            class="bg-[#ca0251] hover:bg-[#a80244] text-white px-6 py-1.5 rounded-lg text-sm transition w-full sm:w-auto">
                        Search
                    </button>
                    @if(request('tenant_code'))
                        <a href="{{ route('tenant.payments.history') }}" class="text-sm text-[#6B7280] hover:text-[#ca0251] transition">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Results Section - Only shown after search -->
            @if(request('tenant_code'))
                
                <!-- Tenant Info -->
                @if(isset($tenant) && $tenant)
                    <div class="mt-4 bg-white border border-[#E5E7EB] rounded-xl p-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                        <span class="text-[#6B7280]">Tenant:</span>
                        <span class="font-medium text-[#111827]">{{ e($tenant->name) }}</span>
                        <span class="text-[#6B7280]">|</span>
                        <span class="text-[#6B7280]">Code:</span>
                        <span class="font-mono text-[#111827]">{{ e($tenant->tenant_code) }}</span>
                        <span class="text-[#6B7280]">|</span>
                        <span class="text-[#6B7280]">Property:</span>
                        <span class="font-medium text-[#111827]">{{ e($tenant->property->name ?? 'N/A') }}</span>
                    </div>
                @elseif(!$errors->has('tenant_code'))
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700 text-sm">
                        No tenant found with code: <strong>{{ e(request('tenant_code')) }}</strong>
                    </div>
                @endif

                <!-- Payments Table -->
                <div class="mt-4 bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
                    <div class="divide-y divide-[#E5E7EB] md:hidden">
                        @if(isset($payments) && $payments->count() > 0)
                            @foreach($payments as $index => $payment)
                                @php
                                    $months = explode(',', $payment->payment_month);
                                    $monthCount = count($months);
                                @endphp
                                <div class="p-4 space-y-2">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            @if($monthCount > 1)
                                                <p class="text-sm text-[#111827]">
                                                    {{ \Carbon\Carbon::createFromFormat('Y-m', trim($months[0]))->format('M Y') }}
                                                    →
                                                    {{ \Carbon\Carbon::createFromFormat('Y-m', trim(end($months)))->format('M Y') }}
                                                </p>
                                                <p class="text-xs text-[#6B7280]">{{ $monthCount }} months</p>
                                            @else
                                                <p class="text-sm text-[#111827]">{{ \Carbon\Carbon::createFromFormat('Y-m', $payment->payment_month)->format('M Y') }}</p>
                                            @endif
                                        </div>
                                        @if($payment->status == 'Pending')
                                            <span class="inline-flex items-center bg-[#ca0251] text-white px-2.5 py-1 rounded-md text-xs font-semibold flex-shrink-0">Pending</span>
                                        @elseif($payment->status == 'Approved')
                                            <span class="inline-flex items-center bg-green-600 text-white px-2.5 py-1 rounded-md text-xs font-semibold flex-shrink-0">Approved</span>
                                        @elseif($payment->status == 'Rejected')
                                            <span class="inline-flex items-center bg-red-600 text-white px-2.5 py-1 rounded-md text-xs font-semibold flex-shrink-0">Rejected</span>
                                        @else
                                            <span class="inline-flex items-center bg-[#F3F4F6] text-[#374151] px-2.5 py-1 rounded-md text-xs font-semibold flex-shrink-0">{{ $payment->status }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-medium text-[#111827]">MK {{ number_format((float)$payment->amount) }}</p>
                                    <p class="text-xs text-[#6B7280]">{{ $payment->created_at ? $payment->created_at->format('d M Y') : 'N/A' }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="px-6 py-8 text-center text-[#6B7280] text-sm">No payment history found for this tenant code.</div>
                        @endif
                    </div>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm min-w-[520px]">
                            <thead class="bg-[#F8FAFC]">
                                <tr class="text-[#6B7280]">
                                    <th class="px-4 py-3 text-left">#</th>
                                    <th class="px-4 py-3 text-left">Period</th>
                                    <th class="px-4 py-3 text-left">Amount</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($payments) && $payments->count() > 0)
                                    @forelse($payments as $index => $payment)
                                        <tr class="border-t border-[#E5E7EB] hover:bg-[#F8FAFC] transition">
                                            <td class="px-4 py-3 text-[#9CA3AF]">
                                                {{ $payments->firstItem() + $index }}
                                            </td>
                                            <td class="px-4 py-3 text-[#111827]">
                                                @php
                                                    $months = explode(',', $payment->payment_month);
                                                    $monthCount = count($months);
                                                @endphp
                                                @if($monthCount > 1)
                                                    <div>
                                                        <span>
                                                            {{ \Carbon\Carbon::createFromFormat('Y-m', trim($months[0]))->format('M Y') }}
                                                            →
                                                            {{ \Carbon\Carbon::createFromFormat('Y-m', trim(end($months)))->format('M Y') }}
                                                        </span>
                                                        <span class="text-xs text-[#6B7280] ml-1">({{ $monthCount }} months)</span>
                                                    </div>
                                                @else
                                                    <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $payment->payment_month)->format('M Y') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-[#111827] font-medium">
                                                MK {{ number_format((float)$payment->amount) }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($payment->status == 'Pending')
                                                    <span class="inline-flex items-center justify-center bg-[#ca0251] text-white px-3 py-1.5 rounded-md text-xs font-semibold min-w-[80px]">
                                                        Pending
                                                    </span>
                                                @elseif($payment->status == 'Approved')
                                                    <span class="inline-flex items-center justify-center bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold min-w-[80px]">
                                                        Approved
                                                    </span>
                                                @elseif($payment->status == 'Rejected')
                                                    <span class="inline-flex items-center justify-center bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold min-w-[80px]">
                                                        Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center bg-[#F3F4F6] text-[#374151] px-3 py-1.5 rounded-md text-xs font-semibold min-w-[80px]">
                                                        {{ $payment->status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-[#6B7280] text-xs">
                                                {{ $payment->created_at ? $payment->created_at->format('d M Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-[#6B7280]">
                                                No payments found for this tenant.
                                            </td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-[#6B7280]">
                                            No payment history found for this tenant code.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if(isset($payments) && method_exists($payments, 'hasPages') && $payments->hasPages())
                        <div class="px-6 py-3.5 border-t border-[#E5E7EB] bg-[#F8FAFC]">
                            {{ $payments->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

            @endif
            <!-- End Results Section -->

            <!-- Empty State - Only shown when no search has been performed -->
            @if(!request('tenant_code'))
                <div class="mt-8 flex flex-col items-center justify-center py-12 text-center">
                    <svg class="w-16 h-16 text-[#D1D5DB] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-[#6B7280] text-sm">Enter your tenant code above to view payment history.</p>
                </div>
            @endif

        </div>
    </div>

    <!-- Add CSS to ensure colors are applied correctly -->
    <style>
        .bg-yellow-500 {
            background-color: #eab308 !important;
        }
        .bg-green-600 {
            background-color: #16a34a !important;
        }
        .bg-red-600 {
            background-color: #dc2626 !important;
        }
        
        /* Optional hover effects */
        .bg-yellow-500:hover {
            background-color: #ca8a04 !important;
        }
        .bg-green-600:hover {
            background-color: #15803d !important;
        }
        .bg-red-600:hover {
            background-color: #b91c1c !important;
        }
        
        /* Pagination Styling */
        .pagination {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .pagination .page-item {
            display: inline-block;
        }
        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 8px;
            border-radius: 8px;
            font-size: 14px;
            color: #374151;
            background: white;
            border: 1px solid #E5E7EB;
            transition: all 0.2s;
            text-decoration: none;
        }
        .pagination .page-link:hover {
            background: #F3F4F6;
            border-color: #D1D5DB;
        }
        .pagination .page-item.active .page-link {
            background: #ca0251;
            color: white;
            border-color: #ca0251;
        }
        .pagination .page-item.disabled .page-link {
            color: #9CA3AF;
            background: #F9FAFB;
            cursor: not-allowed;
            opacity: 0.6;
        }
        .pagination .page-item.disabled .page-link:hover {
            background: #F9FAFB;
        }
    </style>

</body>
</html>