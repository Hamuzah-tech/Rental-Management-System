@extends('layouts.landlord')

@section('title', 'Property Tenants')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-[#111827]">
                {{ $property->name }}
            </h2>
            <p class="text-sm text-[#6B7280] mt-1">
                Manage tenants for this hostel.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-[#6B7280]">
                Tenants: {{ $property->currentTenantCount() }}/{{ $property->max_tenants ?? 0 }}
            </span>
            <a href="{{ route('landlord.properties.index') }}"
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

    {{-- Filters --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <form method="GET" action="{{ route('landlord.properties.show', $property) }}" class="flex flex-wrap items-end gap-3" id="filterForm">
                {{-- Month Filter --}}
                <div>
                    <label for="month" class="block text-sm font-medium text-[#374151] mb-1">Month</label>
                    <select 
                        id="month" 
                        name="month" 
                        class="rounded-lg border-[#E5E7EB] text-sm focus:ring-[#0F172A] focus:border-[#0F172A] py-1.5 px-3 min-w-[160px] bg-white text-[#111827]"
                    >
                        <option value="">All Months</option>
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}" {{ request('month') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Payment Status Filter --}}
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-[#374151] mb-1">Payment Status</label>
                    <select 
                        id="payment_status" 
                        name="payment_status" 
                        class="rounded-lg border-[#E5E7EB] text-sm focus:ring-[#0F172A] focus:border-[#0F172A] py-1.5 px-3 min-w-[140px] bg-white text-[#111827]"
                    >
                        <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>All Tenants</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-[#0F172A] hover:bg-[#1a2a4a] text-white px-6 py-1.5 rounded-lg text-sm transition">
                        Search
                    </button>
                    
                    @if(request('month') || (request('payment_status') && request('payment_status') != 'all'))
                        <a href="{{ route('landlord.properties.show', $property) }}" class="text-sm text-[#6B7280] hover:text-[#111827] transition">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            {{-- PDF Download Button --}}
            <a href="{{ route('landlord.properties.export.pdf', [
                'property' => $property->id,
                'month' => request('month'),
                'payment_status' => request('payment_status', 'all')
            ]) }}"
               class="bg-[#0F172A] hover:bg-[#1a2a4a] text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download PDF
            </a>
        </div>
        
        {{-- Active Filter Display --}}
        @if(request('month') || (request('payment_status') && request('payment_status') != 'all'))
            <div class="flex flex-wrap items-center gap-1.5 mt-3 pt-3 border-t border-[#E5E7EB]">
                <span class="text-xs text-[#6B7280]">Active Filters:</span>
                @if(request('month'))
                    <span class="text-xs bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('M Y') }}
                    </span>
                @endif
                @if(request('payment_status') && request('payment_status') != 'all')
                    <span class="text-xs bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full">
                        {{ request('payment_status') == 'paid' ? 'Paid' : 'Unpaid' }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Tenants Table --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F8FAFC]">
                    <tr class="text-[#6B7280]">
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Tenant Code</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left hidden sm:table-cell">Phone</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">Rent (MK)</th>
                        <th class="px-4 py-3 text-left">Payment</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $index => $tenant)
                        @php
                            $hasPayment = $tenant->payments->count() > 0;
                            $paymentMonths = $tenant->payments->pluck('payment_month')->toArray();
                            $paymentMonthsList = [];
                            foreach ($paymentMonths as $pm) {
                                $months = explode(',', $pm);
                                foreach ($months as $m) {
                                    $paymentMonthsList[] = trim($m);
                                }
                            }
                            $paymentMonthsList = array_unique($paymentMonthsList);
                            sort($paymentMonthsList);
                            
                            // Check if paid for selected month
                            $paidForSelectedMonth = false;
                            if (request('month')) {
                                $selectedMonth = request('month');
                                $paidForSelectedMonth = in_array($selectedMonth, $paymentMonthsList);
                            }
                            
                            // Check if tenant has custom rent
                            $hasCustomRent = $tenant->monthly_rent != $property->monthly_rent;
                        @endphp
                        <tr class="border-t border-[#E5E7EB] hover:bg-[#F8FAFC] transition">
                            <td class="px-4 py-3 text-[#9CA3AF]">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-3 font-mono text-[#111827] text-xs">
                                {{ $tenant->tenant_code }}
                            </td>
                            <td class="px-4 py-3 font-medium text-[#111827]">
                                {{ $tenant->name }}
                            </td>
                            <td class="px-4 py-3 text-[#374151] hidden sm:table-cell">
                                {{ $tenant->phone }}
                            </td>
                            <td class="px-4 py-3 text-[#111827] hidden md:table-cell">
                                {{ number_format($tenant->monthly_rent ?? 0) }}
                                @if($hasCustomRent)
                                    <span class="text-[10px] text-[#6B7280] ml-1">(custom)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($hasPayment)
                                    @if(request('month') && $paidForSelectedMonth)
                                        <span class="inline-block bg-[#F3F4F6] text-[#111827] px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            Paid
                                        </span>
                                        <div class="text-[10px] text-[#6B7280] mt-0.5">
                                            Paid for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('M Y') }}
                                        </div>
                                    @elseif(request('month') && !$paidForSelectedMonth)
                                        <span class="inline-block bg-[#F3F4F6] text-[#6B7280] px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            Unpaid
                                        </span>
                                        <div class="text-[10px] text-[#6B7280] mt-0.5">
                                            Not paid for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('M Y') }}
                                        </div>
                                    @else
                                        <span class="inline-block bg-[#F3F4F6] text-[#111827] px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            Paid
                                        </span>
                                        @if(count($paymentMonthsList) > 0)
                                            <div class="text-[10px] text-[#6B7280] mt-0.5">
                                                {{ implode(', ', array_slice($paymentMonthsList, 0, 2)) }}
                                                @if(count($paymentMonthsList) > 2)
                                                    +{{ count($paymentMonthsList) - 2 }} more
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <span class="inline-block bg-[#F3F4F6] text-[#6B7280] px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        Unpaid
                                    </span>
                                    @if(request('month'))
                                        <div class="text-[10px] text-[#6B7280] mt-0.5">
                                            Not paid for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('M Y') }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                                    {{ $tenant->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <!-- Edit Tenant -->
                                    <a href="{{ route('landlord.tenants.edit', $tenant) }}"
                                       title="Edit Tenant"
                                       class="p-2 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition">
                                        <x-heroicon-o-pencil-square class="w-4 h-4"/>
                                    </a>

                                    <!-- View Tenant -->
                                    <a href="{{ route('landlord.tenants.show', $tenant) }}"
                                       title="View Tenant"
                                       class="p-2 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition">
                                        <x-heroicon-o-eye class="w-4 h-4"/>
                                    </a>

                                    <!-- Delete Tenant -->
                                    <form method="POST"
                                          action="{{ route('landlord.tenants.destroy', $tenant) }}"
                                          id="delete-tenant-{{ $tenant->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            title="Delete Tenant"
                                            onclick="openConfirmModal(
                                                'delete-tenant-{{ $tenant->id }}',
                                                'Delete Tenant',
                                                'Are you sure you want to delete this tenant? This action cannot be undone.'
                                            )"
                                            class="p-2 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition">
                                            <x-heroicon-o-trash class="w-4 h-4"/>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-[#6B7280]">
                                @if(request('month') && request('payment_status') && request('payment_status') != 'all')
                                    No {{ request('payment_status') }} tenants found for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y') }}
                                @elseif(request('month'))
                                    No tenants found for {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('F Y') }}
                                @elseif(request('payment_status') && request('payment_status') != 'all')
                                    No {{ request('payment_status') }} tenants found
                                @else
                                    No tenants found for this property.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Confirmation Modal --}}
<div id="confirmModal" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4" 
     style="display: none;"
     x-cloak>
    <div id="modalBackdrop" 
         class="absolute inset-0 bg-black/30 transition-opacity duration-300"
         style="opacity: 0;">
    </div>
    <div id="modalBox" 
         class="relative bg-white rounded-xl border border-[#E5E7EB] w-full max-w-md p-6 transform transition-all duration-300"
         style="opacity: 0; transform: translateY(20px) scale(0.95);">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-[#F3F4F6] flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-[#6B7280]"/>
            </div>
            <h3 id="modalTitle" class="text-lg font-semibold text-[#111827]"></h3>
        </div>
        <p id="modalMessage" class="text-sm text-[#6B7280] mb-6"></p>
        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <button onclick="closeConfirmModal()" class="px-4 py-2 rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] w-full sm:w-auto transition-colors">
                Cancel
            </button>
            <button onclick="submitConfirmAction()" class="px-4 py-2 rounded-lg bg-[#0F172A] hover:bg-[#1a2a4a] text-white w-full sm:w-auto transition-colors">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
let selectedForm = null;
let isModalOpen = false;

function openConfirmModal(formId, title, message) {
    selectedForm = document.getElementById(formId);
    if (!selectedForm) return;
    
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalMessage').innerText = message;

    const modal = document.getElementById('confirmModal');
    const backdrop = document.getElementById('modalBackdrop');
    const box = document.getElementById('modalBox');

    modal.style.display = 'flex';
    modal.classList.remove('hidden');
    
    requestAnimationFrame(() => {
        backdrop.style.opacity = '1';
        box.style.opacity = '1';
        box.style.transform = 'translateY(0) scale(1)';
    });

    document.body.style.overflow = 'hidden';
    isModalOpen = true;
}

function closeConfirmModal() {
    if (!isModalOpen) return;
    
    const modal = document.getElementById('confirmModal');
    const backdrop = document.getElementById('modalBackdrop');
    const box = document.getElementById('modalBox');

    backdrop.style.opacity = '0';
    box.style.opacity = '0';
    box.style.transform = 'translateY(20px) scale(0.95)';

    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        isModalOpen = false;
    }, 300);
}

function submitConfirmAction() {
    if (selectedForm) {
        selectedForm.submit();
    }
}

document.addEventListener('click', function(event) {
    if (isModalOpen) {
        const modal = document.getElementById('confirmModal');
        if (event.target === modal || event.target === document.getElementById('modalBackdrop')) {
            closeConfirmModal();
        }
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && isModalOpen) {
        closeConfirmModal();
    }
});
</script>

@endsection