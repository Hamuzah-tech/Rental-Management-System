@extends('layouts.landlord')

@section('title','Tenants')

@section('content')

<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800">
                Tenants
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
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

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs sm:text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-slate-500">
                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap">#</th>
                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap">Tenant Code</th>
                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap">Name</th>
                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap hidden sm:table-cell">Phone</th>
                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap hidden md:table-cell">Property</th>
                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap hidden lg:table-cell">Rent</th>
                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap">Status</th>
                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-left whitespace-nowrap">Payment</th>
                        <th class="px-2 sm:px-4 py-2 sm:py-3 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $index => $tenant)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-slate-400">
                                {{ $tenants->firstItem() + $index }}
                            </td>

                            <td class="px-2 sm:px-4 py-2 sm:py-3 font-semibold text-slate-700">
                                {{ $tenant->tenant_code }}
                            </td>

                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-slate-700">
                                {{ $tenant->name }}
                            </td>

                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-slate-600 hidden sm:table-cell">
                                {{ $tenant->phone }}
                            </td>

                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-slate-600 hidden md:table-cell">
                                {{ $tenant->property->name ?? 'N/A' }}
                            </td>

                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-slate-600 hidden lg:table-cell">
                                {{ number_format($tenant->monthly_rent) }}
                            </td>

                            <td class="px-2 sm:px-4 py-2 sm:py-3">
                                @if($tenant->status == 'Active')
                                    <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] sm:text-xs font-medium whitespace-nowrap">
                                        Active
                                    </span>
                                @else
                                    <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] sm:text-xs font-medium whitespace-nowrap">
                                        Moved Out
                                    </span>
                                @endif
                            </td>

                            <td class="px-2 sm:px-4 py-2 sm:py-3">
                                @php
                                    $hasPayment = $tenant->payments()->exists();
                                    $isPaid = $hasPayment;
                                @endphp
                                @if($isPaid)
                                    <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] sm:text-xs font-medium whitespace-nowrap flex items-center gap-0.5 sm:gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Paid
                                    </span>
                                @else
                                    <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full bg-rose-100 text-rose-700 text-[10px] sm:text-xs font-medium whitespace-nowrap flex items-center gap-0.5 sm:gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Unpaid
                                    </span>
                                @endif
                            </td>

                            <td class="px-2 sm:px-4 py-2 sm:py-3">
                                <div class="flex justify-end gap-0.5 sm:gap-1">
                                    <!-- Edit -->
                                    <a href="{{ route('landlord.tenants.edit', $tenant) }}"
                                       title="Edit"
                                       class="p-1.5 sm:p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                                        <x-heroicon-o-pencil-square class="w-4 h-4 sm:w-5 sm:h-5"/>
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
                                                class="p-1.5 sm:p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                                                <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 sm:w-5 sm:h-5"/>
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
                                                class="p-1.5 sm:p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                                                <x-heroicon-o-arrow-path class="w-4 h-4 sm:w-5 sm:h-5"/>
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
                                            class="p-1.5 sm:p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                                            <x-heroicon-o-trash class="w-4 h-4 sm:w-5 sm:h-5"/>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 sm:px-6 py-6 sm:py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <x-heroicon-o-user-group class="w-10 h-10 sm:w-12 sm:h-12 text-slate-300"/>
                                    <p class="text-sm sm:text-base">No tenants found</p>
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
        <div class="text-xs sm:text-sm text-slate-500 order-2 sm:order-1 text-center sm:text-left">
            Showing {{ $tenants->firstItem() ?? 0 }} to {{ $tenants->lastItem() ?? 0 }} of {{ $tenants->total() }} results
        </div>
        <div class="order-1 sm:order-2 w-full sm:w-auto">
            {{ $tenants->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 hidden items-center justify-center z-50 px-4">
    <!-- Background -->
    <div class="absolute inset-0 bg-black/30" onclick="closeConfirmModal()"></div>

    <!-- Modal Box -->
    <div id="modalBox" class="relative bg-white rounded-xl border border-slate-200 w-full max-w-md p-4 sm:p-6 transform translate-y-10 opacity-0 transition duration-300">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 sm:w-6 sm:h-6 text-slate-400"/>
            </div>
            <h3 id="modalTitle" class="text-base sm:text-lg font-semibold text-slate-800"></h3>
        </div>

        <p id="modalMessage" class="text-sm text-slate-500 mb-6"></p>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <button
                onclick="closeConfirmModal()"
                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition w-full sm:w-auto">
                Cancel
            </button>
            <button
                onclick="submitConfirmAction()"
                class="px-4 py-2 rounded-xl bg-slate-800 text-white hover:bg-slate-900 transition w-full sm:w-auto">
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
        background: #1e293b;
        color: white;
        border-color: #1e293b;
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