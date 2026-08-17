@extends('layouts.landlord')

@section('title', 'Property Tenants')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 sm:p-5 page-header">
        <div class="min-w-0">
            <h2 class="text-lg sm:text-xl font-bold text-[#111827] break-anywhere">
                {{ e($property->name) }}
            </h2>
            <p class="text-sm text-[#6B7280] mt-1">
                Manage tenants for this property
            </p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('landlord.properties.index') }}"
               class="bg-[#ca0251] hover:bg-[#a80244] text-white px-4 py-2 rounded-lg text-sm transition flex items-center justify-center gap-2 w-full sm:w-auto">
                <x-heroicon-o-arrow-left class="w-4 h-4"/>
                Back
            </a>
        </div>
    </div>

    {{-- Registration Status Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-[#E5E7EB] p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-medium text-[#374151] mb-1">Registration Status</h3>
                <div class="flex items-center gap-3">
                    @if($property->isRegistrationOpen())
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span>
                            Open
                        </span>
                        <span class="text-xs text-[#6B7280]">
                            Accepting new tenant registrations
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                            <span class="w-2 h-2 bg-red-500 rounded-full inline-block"></span>
                            Closed
                        </span>
                        <span class="text-xs text-[#6B7280]">
                            Not accepting new tenant registrations
                        </span>
                    @endif
                </div>
            </div>
            <form action="{{ route('landlord.properties.toggle-registration', $property) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium rounded-lg border transition whitespace-nowrap
                               @if($property->isRegistrationOpen())
                                   bg-red-50 text-red-700 border-red-200 hover:bg-red-100
                               @else
                                   bg-green-50 text-green-700 border-green-200 hover:bg-green-100
                               @endif">
                    @if($property->isRegistrationOpen())
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pause Registration
                        </span>
                    @else
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Resume Registration
                        </span>
                    @endif
                </button>
            </form>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <form method="GET" action="{{ route('landlord.properties.show', $property->public_id) }}" class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-end gap-3 w-full" id="filterForm">
                {{-- Search Input --}}
                <div class="w-full md:flex-1 md:min-w-[200px]">
                    <label class="block text-sm font-medium text-[#374151] mb-1">
                        Search Tenant
                    </label>
                    <input
                        type="text"
                        name="search"
                        value="{{ e(request('search')) }}"
                        placeholder="Name, Code, Phone or Email"
                        class="rounded-lg border-[#E5E7EB] text-sm focus:ring-[#ca0251] focus:border-[#ca0251] py-1.5 px-3 w-full"
                        maxlength="100">
                </div>

                {{-- Month Filter --}}
                <div class="w-full sm:w-auto">
                    <label for="month" class="block text-sm font-medium text-[#374151] mb-1">Month</label>
                    <select 
                        id="month" 
                        name="month" 
                        class="rounded-lg border-[#E5E7EB] text-sm focus:ring-[#ca0251] focus:border-[#ca0251] py-1.5 px-3 w-full sm:min-w-[140px] bg-white text-[#111827]"
                    >
                        <option value="">All Months</option>
                        @foreach($months as $value => $label)
                            <option value="{{ e($value) }}" {{ request('month') == $value ? 'selected' : '' }}>
                                {{ e($label) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Payment Status Filter --}}
                <div class="w-full sm:w-auto">
                    <label for="payment_status" class="block text-sm font-medium text-[#374151] mb-1">Payment Status</label>
                    <select 
                        id="payment_status" 
                        name="payment_status" 
                        class="rounded-lg border-[#E5E7EB] text-sm focus:ring-[#ca0251] focus:border-[#ca0251] py-1.5 px-3 w-full sm:min-w-[140px] bg-white text-[#111827]"
                    >
                        <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>All Tenants</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>

                {{-- Sort Direction --}}
                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'name') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'asc') }}">

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="bg-[#ca0251] hover:bg-[#a80244] text-white px-6 py-1.5 rounded-lg text-sm transition whitespace-nowrap w-full sm:w-auto">
                        Search
                    </button>
                    
                    @if(request('search') || request('month') || (request('payment_status') && request('payment_status') != 'all') || request('sort_by') || request('sort_dir'))
                        <a href="{{ route('landlord.properties.show', $property->public_id) }}" class="text-sm text-[#6B7280] hover:text-[#ca0251] transition whitespace-nowrap">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            {{-- PDF Text Button --}}
            <a href="{{ route('landlord.properties.export.pdf', [
                'property' => $property->public_id,
                'month' => request('month'),
                'payment_status' => request('payment_status', 'all'),
                'search' => request('search'),
                'sort_by' => request('sort_by', 'name'),
                'sort_dir' => request('sort_dir', 'asc')
            ]) }}"
               target="_blank"
               rel="noopener noreferrer"
               class="text-sm text-[#ca0251] hover:text-[#a80244] font-medium transition whitespace-nowrap">
                Download List
            </a>
        </div>
        
        {{-- Active Filter Display --}}
        @if(request('search') || request('month') || (request('payment_status') && request('payment_status') != 'all') || request('sort_by') || request('sort_dir'))
            <div class="flex flex-wrap items-center gap-1.5 mt-3 pt-3 border-t border-[#E5E7EB]">
                <span class="text-xs text-[#6B7280]">Active Filters:</span>
                @if(request('search'))
                    <span class="text-xs bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full">
                        Search: {{ e(request('search')) }}
                    </span>
                @endif
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
                @if(request('sort_by'))
                    <span class="text-xs bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full">
                        Sort: {{ ucfirst(str_replace('_', ' ', request('sort_by'))) }}
                        {{ request('sort_dir') == 'asc' ? '↑' : '↓' }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Tenants Table --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="divide-y divide-[#E5E7EB] md:hidden">
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
                    $paidForSelectedMonth = request('month') ? in_array(request('month'), $paymentMonthsList) : false;
                    $hasAnyPayment = $hasPayment && count($paymentMonthsList) > 0;
                @endphp
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-[#111827] truncate">{{ e($tenant->name) }}</p>
                            <p class="font-mono text-xs text-[#6B7280] mt-0.5">{{ e($tenant->tenant_code) }}</p>
                        </div>
                        <span class="inline-block bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full text-xs font-medium capitalize flex-shrink-0">
                            {{ e($tenant->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-[#374151] break-anywhere">{{ e($tenant->email) }}</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[#6B7280]">Phone:</span>
                            <span class="text-[#111827]">{{ e($tenant->phone) }}</span>
                        </div>
                        <div>
                            <span class="text-[#6B7280]">Rent:</span>
                            <span class="text-[#111827]">MK {{ number_format((float)($tenant->monthly_rent ?? 0)) }}</span>
                        </div>
                    </div>
                    <div>
                        @if(request('month') ? $paidForSelectedMonth : $hasAnyPayment)
                            <span class="inline-flex items-center bg-green-600 text-white px-2.5 py-1 rounded-md text-xs font-semibold">Paid</span>
                        @else
                            <span class="inline-flex items-center bg-red-600 text-white px-2.5 py-1 rounded-md text-xs font-semibold">Unpaid</span>
                        @endif
                    </div>
                    <div class="flex gap-1 pt-1">
                        <a href="{{ route('landlord.tenants.edit', $tenant) }}" title="Edit Tenant" class="p-2 rounded-lg text-[#6B7280] hover:bg-[#ca0251]/10 hover:text-[#ca0251] transition">
                            <x-heroicon-o-pencil-square class="w-5 h-5"/>
                        </a>
                        <a href="{{ route('landlord.tenants.show', $tenant) }}" title="View Tenant" class="p-2 rounded-lg text-[#6B7280] hover:bg-[#ca0251]/10 hover:text-[#ca0251] transition">
                            <x-heroicon-o-eye class="w-5 h-5"/>
                        </a>
                        <form method="POST" action="{{ route('landlord.tenants.destroy', $tenant) }}" id="delete-tenant-mobile-{{ $tenant->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" title="Delete Tenant"
                                onclick="openConfirmModal('delete-tenant-mobile-{{ $tenant->id }}','Delete Tenant','Are you sure you want to delete this tenant? This action cannot be undone.')"
                                class="p-2 rounded-lg text-[#6B7280] hover:bg-red-50 hover:text-red-600 transition">
                                <x-heroicon-o-trash class="w-5 h-5"/>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-[#6B7280] text-sm">No tenants found for this property.</div>
            @endforelse
        </div>
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead class="bg-[#F8FAFC]">
                    <tr class="text-[#6B7280]">
                        {{-- Tenant Code with Sort --}}
                        <th class="px-4 py-3 text-left font-medium">
                            <div class="flex items-center gap-1 group">
                                <span>Tenant Code</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('landlord.properties.show', array_merge(['property' => $property->public_id], request()->query(), ['sort_by' => 'tenant_code', 'sort_dir' => 'asc'])) }}" 
                                       class="leading-none hover:text-[#ca0251] transition {{ request('sort_by') == 'tenant_code' && request('sort_dir') == 'asc' ? 'text-[#ca0251]' : 'text-[#9CA3AF]' }}">
                                        <svg class="w-3 h-3 -mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('landlord.properties.show', array_merge(['property' => $property->public_id], request()->query(), ['sort_by' => 'tenant_code', 'sort_dir' => 'desc'])) }}" 
                                       class="leading-none hover:text-[#ca0251] transition {{ request('sort_by') == 'tenant_code' && request('sort_dir') == 'desc' ? 'text-[#ca0251]' : 'text-[#9CA3AF]' }}">
                                        <svg class="w-3 h-3 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </th>
                        {{-- Name with Sort --}}
                        <th class="px-4 py-3 text-left font-medium">
                            <div class="flex items-center gap-1 group">
                                <span>Name</span>
                                <div class="flex flex-col">
                                    <a href="{{ route('landlord.properties.show', array_merge(['property' => $property->public_id], request()->query(), ['sort_by' => 'name', 'sort_dir' => 'asc'])) }}" 
                                       class="leading-none hover:text-[#ca0251] transition {{ request('sort_by') == 'name' && request('sort_dir') == 'asc' ? 'text-[#ca0251]' : 'text-[#9CA3AF]' }}">
                                        <svg class="w-3 h-3 -mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('landlord.properties.show', array_merge(['property' => $property->public_id], request()->query(), ['sort_by' => 'name', 'sort_dir' => 'desc'])) }}" 
                                       class="leading-none hover:text-[#ca0251] transition {{ request('sort_by') == 'name' && request('sort_dir') == 'desc' ? 'text-[#ca0251]' : 'text-[#9CA3AF]' }}">
                                        <svg class="w-3 h-3 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-medium">Email</th>
                        <th class="px-4 py-3 text-left font-medium">Phone</th>
                        <th class="px-4 py-3 text-left font-medium">Rent (MK)</th>
                        <th class="px-4 py-3 text-left font-medium">Payment</th>
                        <th class="px-4 py-3 text-left font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
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
                            
                            $paidForSelectedMonth = false;
                            if (request('month')) {
                                $selectedMonth = request('month');
                                $paidForSelectedMonth = in_array($selectedMonth, $paymentMonthsList);
                            }
                            
                            $hasCustomRent = $tenant->monthly_rent != $property->monthly_rent;
                            
                            // Determine if tenant has any payment at all
                            $hasAnyPayment = $hasPayment && count($paymentMonthsList) > 0;
                        @endphp
                        <tr class="border-t border-[#E5E7EB] hover:bg-[#F8FAFC] transition">
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs bg-[#F3F4F6] px-2 py-1 rounded-md text-[#111827]">
                                    {{ e($tenant->tenant_code) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-[#111827]">
                                {{ e($tenant->name) }}
                            </td>
                            <td class="px-4 py-3 text-[#374151]">
                                {{ e($tenant->email) }}
                            </td>
                            <td class="px-4 py-3 text-[#374151]">
                                {{ e($tenant->phone) }}
                            </td>
                            <td class="px-4 py-3 text-[#111827]">
                                {{ number_format((float)($tenant->monthly_rent ?? 0)) }}
                                @if($hasCustomRent)
                                    <span class="text-[10px] text-[#6B7280] ml-1">(custom)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if(request('month'))
                                    {{-- Filtered by specific month --}}
                                    @if($paidForSelectedMonth)
                                        <span class="inline-flex items-center justify-center bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold min-w-[60px]">
                                            Paid
                                        </span>
                                        <div class="text-[10px] text-green-600 mt-0.5">
                                            {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('M Y') }}
                                        </div>
                                    @else
                                        <span class="inline-flex items-center justify-center bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold min-w-[60px]">
                                            Unpaid
                                        </span>
                                        <div class="text-[10px] text-red-600 mt-0.5">
                                            {{ \Carbon\Carbon::createFromFormat('Y-m', request('month'))->format('M Y') }}
                                        </div>
                                    @endif
                                @else
                                    {{-- No month filter - show overall status --}}
                                    @if($hasAnyPayment)
                                        <span class="inline-flex items-center justify-center bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold min-w-[60px]">
                                            Paid
                                        </span>
                                        @if(count($paymentMonthsList) > 0)
                                            <div class="text-[10px] text-green-600 mt-0.5">
                                                {{ implode(', ', array_slice($paymentMonthsList, 0, 2)) }}
                                                @if(count($paymentMonthsList) > 2)
                                                    +{{ count($paymentMonthsList) - 2 }} more
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center justify-center bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold min-w-[60px]">
                                            Unpaid
                                        </span>
                                        <div class="text-[10px] text-red-600 mt-0.5">
                                            No payments recorded
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block bg-[#F3F4F6] text-[#374151] px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                                    {{ e($tenant->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('landlord.tenants.edit', $tenant) }}"
                                       title="Edit Tenant"
                                       class="p-2 rounded-lg text-[#6B7280] hover:bg-[#ca0251]/10 hover:text-[#ca0251] transition">
                                        <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                    </a>

                                    <a href="{{ route('landlord.tenants.show', $tenant) }}"
                                       title="View Tenant"
                                       class="p-2 rounded-lg text-[#6B7280] hover:bg-[#ca0251]/10 hover:text-[#ca0251] transition">
                                        <x-heroicon-o-eye class="w-5 h-5"/>
                                    </a>

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
                                            class="p-2 rounded-lg text-[#6B7280] hover:bg-red-50 hover:text-red-600 transition">
                                            <x-heroicon-o-trash class="w-5 h-5"/>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-[#6B7280]">
                                @if(request('search'))
                                    No tenants found matching "{{ e(request('search')) }}"
                                @elseif(request('month') && request('payment_status') && request('payment_status') != 'all')
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
        
        {{-- Table Footer with Pagination --}}
        <div class="px-4 py-3 border-t border-[#E5E7EB] bg-[#F8FAFC]">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-sm text-[#6B7280]">
                    Showing 
                    <span class="font-medium text-[#111827]">{{ $tenants->firstItem() ?? 0 }}</span>
                    to 
                    <span class="font-medium text-[#111827]">{{ $tenants->lastItem() ?? 0 }}</span>
                    of 
                    <span class="font-medium text-[#111827]">{{ $tenants->total() }}</span>
                    tenants
                </div>
                <div>
                    {{ $tenants->appends(request()->query())->links() }}
                </div>
            </div>
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
            <button onclick="submitConfirmAction()" class="px-4 py-2 rounded-lg bg-[#ca0251] hover:bg-[#a80244] text-white w-full sm:w-auto transition-colors">
                Confirm
            </button>
        </div>
    </div>
</div>

<style>
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
    
    /* Additional styles for consistent green/red */
    .bg-green-600 {
        background-color: #16a34a !important;
    }
    .bg-red-600 {
        background-color: #dc2626 !important;
    }
    .text-green-600 {
        color: #16a34a !important;
    }
    .text-red-600 {
        color: #dc2626 !important;
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

<script>
let selectedForm = null;
let isModalOpen = false;

function openConfirmModal(formId, title, message) {
    selectedForm = document.getElementById(formId);
    if (!selectedForm) return;
    
    // Sanitize inputs to prevent XSS
    const safeTitle = String(title).replace(/[<>]/g, '');
    const safeMessage = String(message).replace(/[<>]/g, '');
    
    document.getElementById('modalTitle').innerText = safeTitle;
    document.getElementById('modalMessage').innerText = safeMessage;

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