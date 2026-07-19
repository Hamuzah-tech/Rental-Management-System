@extends('layouts.landlord')

@section('title', 'Tenants')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Tenants
                </h2>
                <p class="text-gray-500 text-sm mt-1">
                    Manage your tenants.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Export Buttons --}}
                <a href="{{ route('landlord.tenants.export.pdf', ['payment_status' => request('payment_status', 'all')]) }}"
                   class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    PDF
                </a>

                {{-- Add Tenant Button --}}
                <a href="{{ route('landlord.tenants.create') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Tenant
                </a>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('landlord.tenants.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="payment_status" class="text-sm font-medium text-gray-700">Payment Status:</label>
                <select 
                    id="payment_status" 
                    name="payment_status" 
                    onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>All Tenants</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>

            @if(request('payment_status') != 'all')
                <a href="{{ route('landlord.tenants.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    Clear Filter
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs w-12">#</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Tenant Code</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Name</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs hidden sm:table-cell">Phone</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs hidden md:table-cell">Property</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs hidden lg:table-cell">Rent (MK)</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Status</th>
                        <th class="px-6 py-3.5 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Payment</th>
                        <th class="px-6 py-3.5 text-right font-semibold text-gray-500 uppercase tracking-wider text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $index => $tenant)
                        <tr class="border-b border-gray-100 hover:bg-gray-50/70 transition-colors duration-150">
                            <td class="px-4 py-4 text-center text-gray-400 font-medium text-sm">
                                {{ $tenants->firstItem() + $index }}
                            </td>

                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $tenant->tenant_code }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $tenant->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600 hidden sm:table-cell">
                                <a href="tel:{{ $tenant->phone }}" class="hover:underline">
                                    {{ $tenant->phone }}
                                </a>
                            </td>

                            <td class="px-6 py-4 text-gray-600 hidden md:table-cell">
                                {{ $tenant->property->name ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600 hidden lg:table-cell">
                                {{ number_format($tenant->monthly_rent) }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-block bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $tenant->status }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $hasPayment = $tenant->payments()->where('status', 'Approved')->exists();
                                @endphp
                                <span class="inline-block bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $hasPayment ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-1">
                                    {{-- Edit --}}
                                    <a href="{{ route('landlord.tenants.edit', $tenant) }}"
                                       title="Edit"
                                       class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form method="POST"
                                          action="{{ route('landlord.tenants.destroy', $tenant) }}"
                                          id="delete-form-{{ $tenant->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            title="Delete"
                                            onclick="openConfirmModal(
                                                'delete-form-{{ $tenant->id }}',
                                                'Delete Tenant',
                                                'Are you sure you want to delete this tenant? This action cannot be undone.'
                                            )"
                                            class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-150">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-16 text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="text-gray-400">No tenants found</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-500 order-2 sm:order-1">
            Showing {{ $tenants->firstItem() ?? 0 }} to {{ $tenants->lastItem() ?? 0 }} of {{ $tenants->total() }} results
        </div>
        <div class="order-1 sm:order-2 w-full sm:w-auto">
            {{ $tenants->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- Confirmation Modal --}}
<div id="confirmModal" class="fixed inset-0 hidden items-center justify-center z-50 px-4">
    <div class="absolute inset-0 bg-black/30" onclick="closeConfirmModal()"></div>

    <div id="modalBox" class="relative bg-white rounded-xl border border-gray-200 w-full max-w-md p-6 transform translate-y-10 opacity-0 transition duration-300">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-800"></h3>
        </div>

        <p id="modalMessage" class="text-sm text-gray-500 mb-6"></p>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <button
                onclick="closeConfirmModal()"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition w-full sm:w-auto">
                Cancel
            </button>
            <button
                onclick="submitConfirmAction()"
                class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition w-full sm:w-auto">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
let selectedForm = null;

function openConfirmModal(formId, title, message) {
    selectedForm = document.getElementById(formId);
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalMessage').innerText = message;

    const modal = document.getElementById('confirmModal');
    const box = document.getElementById('modalBox');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => {
        box.classList.remove('translate-y-10', 'opacity-0');
    }, 50);
}

function closeConfirmModal() {
    const modal = document.getElementById('confirmModal');
    const box = document.getElementById('modalBox');

    box.classList.add('translate-y-10', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

function submitConfirmAction() {
    if (selectedForm) {
        selectedForm.submit();
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeConfirmModal();
    }
});
</script>

<style>
    .pagination {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        justify-content: center;
    }
    .pagination .page-item {
        display: inline-block;
    }
    .pagination .page-link {
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 13px;
        transition: all 0.2s;
        background: white;
        text-decoration: none;
    }
    .pagination .page-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .pagination .active .page-link {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }
    .pagination .disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (min-width: 640px) {
        .pagination .page-link {
            padding: 8px 14px;
            font-size: 14px;
        }
    }
</style>

@endsection