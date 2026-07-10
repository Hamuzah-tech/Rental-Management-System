@extends('layouts.landlord')

@section('title','Tenants')

@section('page-title','Tenants')

@section('content')

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">
                Tenants
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Manage your tenants.
            </p>
        </div>

        <a href="{{ route('landlord.tenants.create') }}"
           class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm transition flex items-center gap-2 w-full sm:w-auto justify-center">
            <x-heroicon-o-user-plus class="w-4 h-4"/>
            Add Tenant
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-slate-50 border border-slate-200 text-slate-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Section -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <x-heroicon-o-funnel class="w-5 h-5 text-slate-400"/>
                <span class="text-sm font-medium text-slate-700">Filter by Payment Status:</span>
            </div>
            
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <a href="{{ route('landlord.tenants.index', ['status' => 'all']) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition
                   {{ request('status') == 'all' || !request('status') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    All Tenants
                </a>
                <a href="{{ route('landlord.tenants.index', ['status' => 'paid']) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition
                   {{ request('status') == 'paid' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <span class="flex items-center gap-1">
                        <span>✓</span> Paid
                    </span>
                </a>
                <a href="{{ route('landlord.tenants.index', ['status' => 'unpaid']) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition
                   {{ request('status') == 'unpaid' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <span class="flex items-center gap-1">
                        <span>!</span> Unpaid
                    </span>
                </a>
            </div>

            @if(request('status') && request('status') != 'all')
                <a href="{{ route('landlord.tenants.index') }}"
                   class="text-sm text-slate-400 hover:text-slate-600 transition ml-auto">
                    Clear filter ✕
                </a>
            @endif
        </div>

        <!-- Filter Results Summary -->
        <div class="mt-3 text-sm text-slate-500">
            Showing {{ $tenants->firstItem() ?? 0 }} - {{ $tenants->lastItem() ?? 0 }} of {{ $tenants->total() }} tenants
            @if(request('status') && request('status') != 'all')
                <span class="font-medium text-slate-700">
                    ({{ request('status') == 'paid' ? 'Paid' : 'Unpaid' }})
                </span>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-slate-500">
                        <th class="px-4 py-3 text-left whitespace-nowrap">#</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Tenant Code</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Name</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Phone</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Property</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Rent</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Payment</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $index => $tenant)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-400">
                                {{ $tenants->firstItem() + $index }}
                            </td>

                            <td class="px-4 py-3 font-semibold text-slate-700">
                                {{ $tenant->tenant_code }}
                            </td>

                            <td class="px-4 py-3 text-slate-700">
                                {{ $tenant->name }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $tenant->phone }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $tenant->property->name ?? 'N/A' }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ number_format($tenant->monthly_rent) }}
                            </td>

                            <td class="px-4 py-3">
                                @if($tenant->status == 'Active')
                                    <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-medium">
                                        Moved Out
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                @php
                                    // Check if tenant has any payment record (simplified check)
                                    $hasPayment = $tenant->payments()->exists();
                                    $isPaid = $hasPayment; // You can add more logic here based on your payment structure
                                @endphp
                                @if($isPaid)
                                    <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                                        ✓ Paid
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-medium">
                                        ! Unpaid
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <!-- Edit -->
                                    <a href="{{ route('landlord.tenants.edit', $tenant) }}"
                                       title="Edit"
                                       class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                                        <x-heroicon-o-pencil-square class="w-5 h-5"/>
                                    </a>

                                    @if($tenant->status == 'Active')
                                        <!-- Move Out -->
                                        <form method="POST"
                                              action="{{ route('landlord.tenants.moveout', $tenant) }}"
                                              id="moveout-form-{{ $tenant->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="button"
                                                title="Move Out"
                                                onclick="openConfirmModal(
                                                    'moveout-form-{{ $tenant->id }}',
                                                    'Move Out Tenant',
                                                    'Are you sure you want to move out this tenant?'
                                                )"
                                                class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5"/>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Reactivate -->
                                        <form method="POST"
                                              action="{{ route('landlord.tenants.reactivate', $tenant) }}"
                                              id="reactivate-form-{{ $tenant->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="button"
                                                title="Reactivate"
                                                onclick="openConfirmModal(
                                                    'reactivate-form-{{ $tenant->id }}',
                                                    'Reactivate Tenant',
                                                    'Are you sure you want to reactivate this tenant?'
                                                )"
                                                class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                                                <x-heroicon-o-arrow-path class="w-5 h-5"/>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete -->
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
                                            class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                                            <x-heroicon-o-trash class="w-5 h-5"/>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <x-heroicon-o-user-group class="w-12 h-12 text-slate-300"/>
                                    <p>No tenants found</p>
                                    @if(request('status') && request('status') != 'all')
                                        <p class="text-sm text-slate-400">
                                            No tenants match the selected filter
                                        </p>
                                        <a href="{{ route('landlord.tenants.index') }}"
                                           class="text-sm text-slate-600 hover:text-slate-800 underline">
                                            Clear filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-slate-500 order-2 sm:order-1">
            Showing {{ $tenants->firstItem() ?? 0 }} to {{ $tenants->lastItem() ?? 0 }} of {{ $tenants->total() }} results
        </div>
        <div class="order-1 sm:order-2 w-full sm:w-auto">
            {{ $tenants->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <!-- Background -->
    <div class="absolute inset-0 bg-black/30" onclick="closeConfirmModal()"></div>

    <!-- Modal Box -->
    <div id="modalBox" class="relative bg-white rounded-xl border border-slate-200 w-full max-w-md p-6 transform translate-y-10 opacity-0 transition duration-300">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-slate-400"/>
            </div>
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-800"></h3>
        </div>

        <p id="modalMessage" class="text-sm text-slate-500 mb-6"></p>

        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <button
                onclick="closeConfirmModal()"
                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition order-2 sm:order-1">
                Cancel
            </button>
            <button
                onclick="submitConfirmAction()"
                class="px-4 py-2 rounded-xl bg-slate-800 text-white hover:bg-slate-900 transition order-1 sm:order-2">
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

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeConfirmModal();
    }
});
</script>

<style>
    /* Custom pagination styling */
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
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
        text-decoration: none;
    }
    .pagination .page-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .pagination .active .page-link {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
    }
    .pagination .disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 640px) {
        .pagination .page-link {
            padding: 6px 10px;
            font-size: 12px;
        }
    }
</style>

@endsection