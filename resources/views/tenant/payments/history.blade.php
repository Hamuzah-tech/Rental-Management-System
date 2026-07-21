<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History · Tenant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F8FAFC]">

    <div class="min-h-screen px-4 py-8">
        <div class="max-w-4xl mx-auto">

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('tenant.payments.index') }}"
                   class="inline-flex items-center gap-2 text-[#6B7280] hover:text-[#111827] transition text-sm">
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
                <div class="mt-4 p-3 bg-[#F3F4F6] border border-[#E5E7EB] rounded-lg text-[#111827] text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-red-600 text-xs list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Search Form -->
            <div class="mt-6 bg-white border border-[#E5E7EB] rounded-xl p-4">
                <form method="GET" action="{{ route('tenant.payments.history') }}" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-medium text-[#374151] mb-1">Tenant Code</label>
                        <input type="text" 
                               name="tenant_code" 
                               value="{{ request('tenant_code') }}"
                               placeholder="Enter your tenant code"
                               class="w-full rounded-lg border-[#E5E7EB] focus:border-[#0F172A] focus:ring-[#0F172A] px-3 py-1.5 text-sm bg-white text-[#111827]">
                    </div>
                    <button type="submit" 
                            class="bg-[#0F172A] hover:bg-[#1a2a4a] text-white px-6 py-1.5 rounded-lg text-sm transition">
                        Search
                    </button>
                    @if(request('tenant_code'))
                        <a href="{{ route('tenant.payments.history') }}" class="text-sm text-[#6B7280] hover:text-[#111827]">
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
                        <span class="font-medium text-[#111827]">{{ $tenant->name }}</span>
                        <span class="text-[#6B7280]">|</span>
                        <span class="text-[#6B7280]">Code:</span>
                        <span class="font-mono text-[#111827]">{{ $tenant->tenant_code }}</span>
                        <span class="text-[#6B7280]">|</span>
                        <span class="text-[#6B7280]">Property:</span>
                        <span class="font-medium text-[#111827]">{{ $tenant->property->name ?? 'N/A' }}</span>
                    </div>
                @elseif(!$errors->has('tenant_code'))
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700 text-sm">
                        No tenant found with code: <strong>{{ request('tenant_code') }}</strong>
                    </div>
                @endif

                <!-- Payments Table -->
                <div class="mt-4 bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
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
                                                MK {{ number_format($payment->amount) }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($payment->status == 'Pending')
                                                    <span class="inline-block bg-[#F3F4F6] text-[#6B7280] px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                        Pending
                                                    </span>
                                                @elseif($payment->status == 'Approved')
                                                    <span class="inline-block bg-[#F3F4F6] text-[#111827] px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                        Approved
                                                    </span>
                                                @elseif($payment->status == 'Rejected')
                                                    <span class="inline-block bg-[#F3F4F6] text-[#6B7280] px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                        Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-block bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full text-xs font-medium">
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

</body>
</html>