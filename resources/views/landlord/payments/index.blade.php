@extends('layouts.landlord')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')

<div class="space-y-6">

    {{-- Header Card --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 sm:p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#111827]">
                    Tenant Payments
                </h2>
                <p class="text-sm text-[#6B7280] mt-1">Review and approve tenant payments.</p>
            </div>

            <form method="GET" action="{{ route('landlord.payments.index') }}" id="filterForm" class="flex flex-wrap items-end gap-3">
                {{-- Hostel Filter --}}
                <div>
                    <label for="property_id" class="block text-sm font-medium text-[#374151] mb-1">Hostel</label>
                    <select
                        id="property_id"
                        name="property_id"
                        class="rounded-lg border-[#E5E7EB] text-sm focus:ring-[#0F172A] focus:border-[#0F172A] py-1.5 px-3 min-w-[160px] bg-white text-[#111827]"
                    >
                        <option value="">All Hostels</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Month Filter --}}
                <div>
                    <label for="month" class="block text-sm font-medium text-[#374151] mb-1">Month</label>
                    <select
                        id="month"
                        name="month"
                        class="rounded-lg border-[#E5E7EB] text-sm focus:ring-[#0F172A] focus:border-[#0F172A] py-1.5 px-3 min-w-[160px] bg-white text-[#111827]"
                    >
                        <option value="">All Months</option>
                        @if(isset($months) && count($months) > 0)
                            @foreach($months as $value => $label)
                                <option value="{{ $value }}" {{ request('month') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>No months available</option>
                        @endif
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-[#374151] mb-1">Status</label>
                    <select
                        id="status"
                        name="status"
                        class="rounded-lg border-[#E5E7EB] text-sm focus:ring-[#0F172A] focus:border-[#0F172A] py-1.5 px-3 min-w-[140px] bg-white text-[#111827]"
                    >
                        <option value="">All Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-[#0F172A] hover:bg-[#1a2a4a] text-white px-6 py-1.5 rounded-lg text-sm transition">
                        Search
                    </button>
                    
                    @if(request('property_id') || request('month') || request('status'))
                        <a href="{{ route('landlord.payments.index') }}" class="text-sm text-[#6B7280] hover:text-[#111827] transition">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Active Filters Display --}}
    @if(request('property_id') || request('month') || request('status'))
        <div class="bg-[#F8FAFC] border border-[#E5E7EB] rounded-lg px-4 py-2.5 flex flex-wrap items-center gap-2 text-sm">
            <span class="text-[#6B7280] font-medium">Active Filters:</span>
            @if(request('property_id'))
                <span class="bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full text-xs">
                    @php
                        $prop = \App\Models\Property::find(request('property_id'));
                    @endphp
                    Hostel: {{ $prop->name ?? 'N/A' }}
                </span>
            @endif
            @if(request('month'))
                <span class="bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full text-xs">
                    Month: {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y') }}
                </span>
            @endif
            @if(request('status'))
                <span class="bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full text-xs">
                    Status: {{ request('status') }}
                </span>
            @endif
        </div>
    @endif

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="bg-[#F3F4F6] border border-[#E5E7EB] text-[#111827] px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Payments Table --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-[#E5E7EB]">
                        <th class="px-4 py-3.5 text-left font-semibold text-[#6B7280] uppercase tracking-wider text-xs w-12">#</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-[#6B7280] uppercase tracking-wider text-xs">Tenant</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-[#6B7280] uppercase tracking-wider text-xs">Hostel</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-[#6B7280] uppercase tracking-wider text-xs">Period</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-[#6B7280] uppercase tracking-wider text-xs">Amount</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-[#6B7280] uppercase tracking-wider text-xs">Status</th>
                        <th class="px-6 py-3.5 text-center font-semibold text-[#6B7280] uppercase tracking-wider text-xs">Action</th>
                    </tr>
                </thead>
                <tbody>

                @forelse($payments as $payment)
                    <tr class="border-b border-[#E5E7EB] hover:bg-[#F8FAFC] transition-colors duration-150">
                        <td class="px-4 py-4 text-center text-[#9CA3AF] font-medium text-sm">
                            {{ $payments->firstItem() + $loop->index }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-[#111827]">{{ $payment->tenant->name ?? 'N/A' }}</span>
                            <div class="text-xs text-[#6B7280]">{{ $payment->tenant->tenant_code ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[#374151]">{{ $payment->tenant->property->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $months = explode(',', $payment->payment_month);
                                $monthCount = count($months);
                            @endphp
                            
                            @if($monthCount > 1)
                                <div class="flex flex-col">
                                    <span class="text-[#111827] font-medium">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', trim($months[0]))->format('M Y') }}
                                        <span class="text-[#6B7280] mx-1">→</span>
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', trim(end($months)))->format('M Y') }}
                                    </span>
                                    <span class="text-xs text-[#6B7280] font-medium mt-0.5">
                                        {{ $monthCount }} months
                                    </span>
                                </div>
                            @else
                                <span class="text-[#374151]">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $payment->payment_month)->format('M Y') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-[#111827]">
                                MK {{ number_format($payment->amount) }}
                            </span>
                            @if($monthCount > 1)
                                <div class="text-xs text-[#6B7280]">
                                    {{ number_format($payment->amount / $monthCount, 2) }} per month
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($payment->status == 'Pending')
                                <span class="inline-block bg-[#F3F4F6] text-[#6B7280] px-3 py-1 rounded-full text-xs font-medium">
                                    Pending
                                </span>
                            @elseif($payment->status == 'Approved')
                                <span class="inline-block bg-[#F3F4F6] text-[#111827] px-3 py-1 rounded-full text-xs font-medium">
                                    Approved
                                </span>
                            @elseif($payment->status == 'Rejected')
                                <span class="inline-block bg-[#F3F4F6] text-[#6B7280] px-3 py-1 rounded-full text-xs font-medium">
                                    Rejected
                                </span>
                            @else
                                <span class="inline-block bg-[#F3F4F6] text-[#374151] px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $payment->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a
                                    href="{{ route('landlord.payments.show', $payment) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-[#6B7280] hover:text-[#111827] hover:bg-[#F3F4F6] rounded-lg transition-colors duration-150"
                                    title="View Payment">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                @if($payment->status == 'Pending')
                                    {{-- Approve --}}
                                    <form method="POST" action="{{ route('landlord.payments.approve', $payment) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 text-[#6B7280] hover:text-[#111827] hover:bg-[#F3F4F6] rounded-lg transition-colors duration-150"
                                                title="Approve Payment"
                                                onclick="return confirm('Approve this payment?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- Reject --}}
                                    <form method="POST" action="{{ route('landlord.payments.reject', $payment) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 text-[#6B7280] hover:text-[#111827] hover:bg-[#F3F4F6] rounded-lg transition-colors duration-150"
                                                title="Reject Payment"
                                                onclick="return confirm('Reject this payment?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-16 text-[#6B7280]">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-[#E5E7EB] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <span class="text-[#6B7280]">
                                    @if(request('property_id') && request('month') && request('status'))
                                        No {{ request('status') }} payments found for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y') }} in the selected hostel
                                    @elseif(request('property_id') && request('month'))
                                        No payments found for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y') }} in the selected hostel
                                    @elseif(request('property_id') && request('status'))
                                        No {{ request('status') }} payments found in the selected hostel
                                    @elseif(request('property_id'))
                                        No payments found in the selected hostel
                                    @elseif(request('month') && request('status'))
                                        No {{ request('status') }} payments found for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y') }}
                                    @elseif(request('month'))
                                        No payments found for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y') }}
                                    @elseif(request('status'))
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
            <div class="px-6 py-3.5 border-t border-[#E5E7EB] bg-[#F8FAFC]">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@endsection