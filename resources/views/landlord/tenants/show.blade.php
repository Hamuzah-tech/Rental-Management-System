@extends('layouts.landlord')

@section('title', 'Tenant Details')
@section('page-title', 'Tenant Details')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-[#111827]">
                {{ $tenant->name }}
            </h2>
            <p class="text-sm text-[#6B7280] mt-1">
                Tenant details and payment history.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('landlord.tenants.index') }}"
               class="bg-[#0F172A] hover:bg-[#1a2a4a] text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                <x-heroicon-o-arrow-left class="w-4 h-4"/>
                Back
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-[#F3F4F6] border border-[#E5E7EB] text-[#111827] px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tenant Information Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4">
            <p class="text-xs text-[#6B7280]">Tenant Code</p>
            <p class="text-sm font-mono text-[#111827] mt-1">{{ $tenant->tenant_code }}</p>
        </div>
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4">
            <p class="text-xs text-[#6B7280]">Property</p>
            <p class="text-sm font-medium text-[#111827] mt-1">{{ $tenant->property->name ?? 'N/A' }}</p>
        </div>
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4">
            <p class="text-xs text-[#6B7280]">Monthly Rent</p>
            <p class="text-sm font-bold text-[#111827] mt-1">MK {{ number_format($tenant->monthly_rent ?? 0) }}</p>
        </div>
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4">
            <p class="text-xs text-[#6B7280]">Status</p>
            <span class="inline-block mt-1 bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                {{ $tenant->status }}
            </span>
        </div>
    </div>

    {{-- Tenant Details --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E5E7EB]">
            <h3 class="font-semibold text-[#111827] text-sm">Contact Information</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-[#6B7280]">Full Name</p>
                <p class="text-sm text-[#111827] font-medium">{{ $tenant->name }}</p>
            </div>
            <div>
                <p class="text-xs text-[#6B7280]">Email</p>
                <p class="text-sm text-[#111827]">{{ $tenant->email }}</p>
            </div>
            <div>
                <p class="text-xs text-[#6B7280]">Phone Number</p>
                <p class="text-sm text-[#111827]">{{ $tenant->phone }}</p>
            </div>
            <div>
                <p class="text-xs text-[#6B7280]">Move In Date</p>
                <p class="text-sm text-[#111827]">{{ $tenant->move_in_date ? date('F d, Y', strtotime($tenant->move_in_date)) : 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E5E7EB] flex justify-between items-center">
            <h3 class="font-semibold text-[#111827] text-sm">Payment History</h3>
            <span class="text-xs text-[#6B7280]">
                Total Paid: MK {{ number_format($tenant->payments->where('status', 'Approved')->sum('amount')) }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F8FAFC]">
                    <tr class="text-[#6B7280]">
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Month(s)</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenant->payments as $index => $payment)
                        <tr class="border-t border-[#E5E7EB] hover:bg-[#F8FAFC] transition">
                            <td class="px-4 py-3 text-[#9CA3AF]">
                                {{ $loop->iteration }}
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
                                No payment history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection