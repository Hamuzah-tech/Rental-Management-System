@extends('layouts.landlord')

@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('content')

<div class="space-y-6">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-[#ca0251]/10 border border-[#ca0251] text-[#ca0251] px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header with Add Tenant Button --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-[#111827]">Tenants</h2>
            <p class="text-sm text-[#6B7280] mt-1">Manage your tenants.</p>
        </div>

        <a href="{{ route('landlord.tenants.create') }}"
           class="bg-[#ca0251] hover:bg-[#a80244] text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2 w-full sm:w-auto justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Tenant
        </a>
    </div>

    {{-- Tenants Table --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="divide-y divide-[#E5E7EB] md:hidden">
            @forelse($tenants as $index => $tenant)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-[#111827] truncate">{{ e($tenant->name) }}</p>
                            <p class="font-mono text-xs text-[#6B7280] mt-0.5">{{ e($tenant->tenant_code) }}</p>
                        </div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium capitalize flex-shrink-0
                            @if($tenant->status == 'active') bg-green-100 text-green-700
                            @elseif($tenant->status == 'inactive') bg-[#F3F4F6] text-[#6B7280]
                            @else bg-[#F3F4F6] text-[#374151] @endif">
                            {{ e($tenant->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-[#374151] break-anywhere">{{ e($tenant->email) }}</p>
                    <p class="text-sm text-[#374151]">{{ e($tenant->phone) }}</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[#6B7280]">Hostel:</span>
                            <span class="text-[#111827]">{{ e($tenant->property->name ?? 'N/A') }}</span>
                        </div>
                        <div>
                            <span class="text-[#6B7280]">Rent:</span>
                            <span class="text-[#111827]">MK {{ number_format((float)($tenant->monthly_rent ?? 0)) }}</span>
                        </div>
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
                <div class="px-6 py-8 text-center text-[#6B7280] text-sm">No tenants found.</div>
            @endforelse
        </div>
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead class="bg-[#F8FAFC]">
                    <tr class="text-[#6B7280]">
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">Tenant Code</th>
                        <th class="px-4 py-3 text-left font-medium">Name</th>
                        <th class="px-4 py-3 text-left font-medium">Email</th>
                        <th class="px-4 py-3 text-left font-medium">Phone</th>
                        <th class="px-4 py-3 text-left font-medium">Hostel</th>
                        <th class="px-4 py-3 text-left font-medium">Rent (MK)</th>
                        <th class="px-4 py-3 text-left font-medium">Status</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $index => $tenant)
                        <tr class="border-t border-[#E5E7EB] hover:bg-[#F8FAFC] transition">
                            <td class="px-4 py-3 text-[#9CA3AF] text-center">
                                {{ $tenants->firstItem() + $index }}
                            </td>
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
                            <td class="px-4 py-3 text-[#374151]">
                                {{ e($tenant->property->name ?? 'N/A') }}
                            </td>
                            <td class="px-4 py-3 text-[#111827]">
                                {{ number_format((float)($tenant->monthly_rent ?? 0)) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                    @if($tenant->status == 'active') bg-green-100 text-green-700
                                    @elseif($tenant->status == 'inactive') bg-[#F3F4F6] text-[#6B7280]
                                    @else bg-[#F3F4F6] text-[#374151] @endif">
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
                            <td colspan="9" class="px-6 py-8 text-center text-[#6B7280]">
                                No tenants found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer with Pagination --}}
        @if($tenants->count() > 0)
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
        @endif
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