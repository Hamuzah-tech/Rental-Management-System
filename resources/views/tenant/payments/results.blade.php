<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment History</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-white min-h-screen">

<div class="min-h-screen flex items-center justify-center px-4 py-6">

    <div class="w-full max-w-4xl">

        <!-- Header -->
        <div class="flex items-center justify-between mb-5">

            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Payment History
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Your rental payment records.
                </p>
            </div>

            <a href="{{ route('tenant.payments.index') }}"
               class="flex items-center gap-1 border border-slate-300 hover:border-[#ca0251] hover:text-[#ca0251] hover:bg-[#ca0251]/10 text-slate-700 px-3 py-2 rounded-lg text-sm transition">

                <x-heroicon-o-arrow-left class="w-4 h-4"/>
                Back

            </a>

        </div>

        <!-- Tenant Information -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 mb-5">

            <div class="flex items-center gap-2 mb-4">

                <x-heroicon-o-user-circle class="w-8 h-8 text-[#ca0251]"/>

                <h2 class="font-semibold text-slate-800">
                    Tenant Details
                </h2>

            </div>

            <div class="space-y-2 text-sm">

                <div class="flex justify-between">
                    <span class="text-slate-500">Name</span>
                    <span class="font-medium text-slate-800">{{ e($tenant->name) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Property</span>
                    <span class="font-medium text-slate-800">{{ e($tenant->property->name ?? 'N/A') }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Code</span>
                    <span class="font-medium text-slate-800">{{ e($tenant->tenant_code) }}</span>
                </div>

            </div>

        </div>

        <!-- Payment Records -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">

            <div class="px-4 py-3 border-b border-slate-200 flex items-center gap-2">

                <x-heroicon-o-document-text class="w-6 h-6 text-[#ca0251]"/>

                <h2 class="font-semibold text-slate-800">
                    Payments
                </h2>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="bg-slate-50">

                    <tr>
                        <th class="px-4 py-3 text-left text-slate-600">Period</th>
                        <th class="px-4 py-3 text-left text-slate-600">Amount</th>
                        <th class="px-4 py-3 text-left text-slate-600">Status</th>
                    </tr>

                    </thead>

                    <tbody>

                    @forelse($payments as $payment)
                    <tr class="border-t border-slate-100 hover:bg-[#F8FAFC] transition">

                        <td class="px-4 py-3 text-slate-700">
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

                        <td class="px-4 py-3 text-slate-700 font-medium">
                            MK {{ number_format((float)$payment->amount) }}
                        </td>

                        <td class="px-4 py-3">

                            @if($payment->status == 'Approved')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    Approved
                                </span>
                            @elseif($payment->status == 'Rejected')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    Rejected
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-[#ca0251]/10 text-[#ca0251]">
                                    Pending
                                </span>
                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-slate-500">
                            No payments found.
                        </td>
                    </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            @if(isset($payments) && method_exists($payments, 'hasPages') && $payments->hasPages())
                <div class="px-6 py-3.5 border-t border-[#E5E7EB] bg-[#F8FAFC]">
                    {{ $payments->appends(request()->query())->links() }}
                </div>
            @endif

        </div>

    </div>

</div>

<style>
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