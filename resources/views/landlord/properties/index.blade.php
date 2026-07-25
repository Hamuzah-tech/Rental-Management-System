@extends('layouts.landlord')

@section('title','Properties')

@section('content')

<div class="space-y-4 sm:space-y-6">

<!-- Header -->
<div class="bg-white border border-[#E5E7EB] rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
    <div>
        <h2 class="text-lg sm:text-xl font-bold text-[#111827]">
           Hostels
        </h2>
        <p class="text-xs sm:text-sm text-[#6B7280] mt-0.5">
            Manage your Hostel(s).
        </p>
    </div>

    <a href="{{ route('landlord.properties.create') }}"
       class="bg-[#0F172A] hover:bg-[#1a2a4a] text-white px-4 py-2 rounded-lg text-sm transition w-full sm:w-auto text-center">
        Add Hostel
    </a>
</div>

<!-- Success -->
@if(session('success'))
    <div class="bg-[#F3F4F6] border border-[#E5E7EB] text-[#111827] px-3 py-2.5 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif

<!-- Table -->
<div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">

    <!-- Mobile Card View -->
    <div class="block sm:hidden divide-y divide-[#E5E7EB]">
        @forelse($properties as $index => $property)
            <div class="p-3 hover:bg-[#F8FAFC] transition">
                <div class="flex justify-between items-start mb-1.5">
                    <div class="flex-1">
                        <h3 class="font-semibold text-[#111827] text-sm truncate">
                            {{ $property->name }}
                        </h3>
                    </div>
                    <span class="text-xs text-[#9CA3AF] flex-shrink-0 ml-2">
                        #{{ $properties->firstItem() + $index }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-1.5 mt-1.5">
                    <div class="text-xs">
                        <span class="text-[#6B7280]">Rent:</span>
                        <span class="font-medium text-[#111827]">MK {{ number_format($property->monthly_rent ?? 0) }}</span>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mt-2.5">
                    <div>
                        @if($property->status)
                            <span class="px-2 py-0.5 rounded-full bg-[#F3F4F6] text-[#374151] text-[10px] font-medium">
                                Active
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-[#F3F4F6] text-[#6B7280] text-[10px] font-medium">
                                Inactive
                            </span>
                        @endif
                        @if($property->isFull())
                            <span class="ml-1 px-1.5 py-0.5 bg-[#E5E7EB] text-[#374151] text-[10px] rounded-full">Full</span>
                        @endif
                    </div>
                    
                    <div class="flex gap-0.5">
                        <a href="{{ route('landlord.properties.show', $property) }}"
                           title="View Tenants"
                           class="p-1.5 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition">
                            <x-heroicon-o-eye class="w-4 h-4"/>
                        </a>

                        <a href="{{ route('landlord.properties.edit', $property) }}"
                           title="Edit"
                           class="p-1.5 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition">
                            <x-heroicon-o-pencil-square class="w-4 h-4"/>
                        </a>

                        <form method="POST"
                              action="{{ route('landlord.properties.destroy',$property) }}"
                              id="delete-property-mobile-{{ $property->id }}">
                            @csrf
                            @method('DELETE')
                            <button
                                type="button"
                                title="Delete"
                                onclick="openConfirmModal(
                                    'delete-property-mobile-{{ $property->id }}',
                                    'Delete Hostel',
                                    'Are you sure you want to delete this Hostel? This action cannot be undone.'
                                )"
                                class="p-1.5 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition">
                                <x-heroicon-o-trash class="w-4 h-4"/>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-[#6B7280] text-sm">
                No Hostels found.
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View -->
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-[#F8FAFC]">
                <tr class="text-[#6B7280]">
                    <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider">#</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider">Rent (MK)</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider">Tenants</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $index => $property)
                    <tr class="border-t border-[#E5E7EB] hover:bg-[#F8FAFC] transition">
                        <td class="px-4 py-2.5 text-[#9CA3AF] text-center">
                            {{ $properties->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-2.5 font-medium text-[#111827]">
                            {{ $property->name }}
                        </td>
                        <td class="px-4 py-2.5 text-[#111827]">
                            {{ number_format($property->monthly_rent ?? 0) }}
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="text-[#111827]">
                                {{ $property->currentTenantCount() }}/{{ $property->max_tenants ?? 0 }}
                            </span>
                            @if($property->isFull())
                                <span class="ml-1 px-1.5 py-0.5 bg-[#E5E7EB] text-[#374151] text-[10px] rounded-full">Full</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5">
                            @if($property->status)
                                <span class="px-2 py-0.5 rounded-full bg-[#F3F4F6] text-[#374151] text-[10px] font-medium">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-[#F3F4F6] text-[#6B7280] text-[10px] font-medium">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5">
                            <div class="flex justify-end gap-0.5">
                                <a href="{{ route('landlord.properties.show', $property) }}"
                                   title="View Tenants"
                                   class="p-1.5 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition">
                                    <x-heroicon-o-eye class="w-4 h-4"/>
                                </a>

                                <a href="{{ route('landlord.properties.edit', $property) }}"
                                   title="Edit"
                                   class="p-1.5 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition">
                                    <x-heroicon-o-pencil-square class="w-4 h-4"/>
                                </a>

                                <form method="POST"
                                      action="{{ route('landlord.properties.destroy',$property) }}"
                                      id="delete-property-{{ $property->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        title="Delete"
                                        onclick="openConfirmModal(
                                            'delete-property-{{ $property->id }}',
                                            'Delete Property',
                                            'Are you sure you want to delete this property? This action cannot be undone.'
                                        )"
                                        class="p-1.5 rounded-lg text-[#6B7280] hover:bg-[#F3F4F6] hover:text-[#111827] transition">
                                        <x-heroicon-o-trash class="w-4 h-4"/>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-[#6B7280] text-sm">
                            No Hostels found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="px-2 sm:px-0">
    {{ $properties->links() }}
</div>

</div>

<!-- Confirmation Modal -->
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
            <h3 id="modalTitle" class="text-lg font-semibold text-[#111827]">
            </h3>
        </div>

        <p id="modalMessage" class="text-sm text-[#6B7280] mb-6">
        </p>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <button
                onclick="closeConfirmModal()"
                class="px-4 py-2 rounded-lg border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] w-full sm:w-auto transition-colors">
                Cancel
            </button>
            <button
                onclick="submitConfirmAction()"
                class="px-4 py-2 rounded-lg bg-[#0F172A] hover:bg-[#1a2a4a] text-white w-full sm:w-auto transition-colors">
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
    if (!selectedForm) {
        console.error('Form not found:', formId);
        return;
    }
    
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