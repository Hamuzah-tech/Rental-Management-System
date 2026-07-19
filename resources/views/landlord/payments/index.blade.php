@extends('layouts.landlord')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')

<div class="space-y-6">

    {{-- Header Card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Tenant Payments
                </h2>
                <p class="text-gray-500 text-sm mt-1">Review and approve tenant payments.</p>
            </div>

            <div>
                <form method="GET" action="{{ route('landlord.payments.index') }}">
                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white text-gray-700 min-w-[160px]">
                        <option value="">All Payments</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Payments Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs w-12">No.</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Tenant</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Property</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Month</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Amount</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Status</th>
                        <th class="px-6 py-3.5 text-center font-semibold text-gray-500 uppercase tracking-wider text-xs">Action</th>
                    </tr>
                </thead>
                <tbody>

                @forelse($payments as $payment)
                    <tr class="border-b border-gray-100 hover:bg-gray-50/70 transition-colors duration-150">
                        <td class="px-4 py-4 text-center text-gray-400 font-medium text-sm">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-800">{{ $payment->tenant->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600">{{ $payment->tenant->property->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600">{{ $payment->payment_month }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-800">MK {{ number_format($payment->amount) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a
                                href="{{ route('landlord.payments.show', $payment) }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-150"
                                title="View Payment">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-16 text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <span class="text-gray-400">
                                    @if(request('status'))
                                        No {{ request('status') }} payments found
                                    @else
                                        No payments found
                                    @endif
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($payments->hasPages())
            <div class="px-6 py-3.5 border-t border-gray-200 bg-gray-50/50">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@endsection