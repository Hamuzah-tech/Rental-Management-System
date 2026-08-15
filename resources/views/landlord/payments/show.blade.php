@extends('layouts.landlord')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    @if(session('success'))
        <div class="bg-[#F3F4F6] border border-[#E5E7EB] text-[#111827] px-4 py-2 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white rounded-lg border border-[#E5E7EB] px-4 sm:px-5 py-3 page-header">
        <div>
            <h2 class="text-base font-semibold text-[#111827]">Payment Information</h2>
            <p class="text-[#6B7280] text-xs">Review this tenant payment.</p>
        </div>
        <a href="{{ route('landlord.payments.index') }}" class="text-[#ca0251] hover:text-[#a80244] text-sm transition">
            ← Back to Payments
        </a>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Payment Details -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-[#E5E7EB] px-5 py-4">
            <h3 class="font-semibold text-sm mb-3 text-[#111827]">Payment Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-sm">
                <div>
                    <p class="text-[#6B7280] text-xs mb-0.5">Tenant</p>
                    <p class="text-[#111827] font-medium">{{ $payment->tenant->name }}</p>
                </div>
                <div>
                    <p class="text-[#6B7280] text-xs mb-0.5">Tenant Code</p>
                    <p class="text-[#111827] font-mono">{{ $payment->tenant->tenant_code }}</p>
                </div>
                <div>
                    <p class="text-[#6B7280] text-xs mb-0.5">Property</p>
                    <p class="text-[#111827] font-medium">{{ $payment->tenant->property->name }}</p>
                </div>
                <div>
                    <p class="text-[#6B7280] text-xs mb-0.5">Payment Period</p>
                    @php
                        $months = explode(',', $payment->payment_month);
                        $monthCount = count($months);
                    @endphp
                    <div>
                        @if($monthCount > 1)
                            <p class="text-[#111827] font-medium">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', trim($months[0]))->format('M Y') }}
                                →
                                {{ \Carbon\Carbon::createFromFormat('Y-m', trim(end($months)))->format('M Y') }}
                            </p>
                            <span class="text-xs text-[#6B7280] font-medium">
                                ({{ $monthCount }} months)
                            </span>
                        @else
                            <p class="text-[#111827]">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $payment->payment_month)->format('F Y') }}
                            </p>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-[#6B7280] text-xs mb-0.5">Total Amount</p>
                    <p class="text-[#111827] font-medium">MK {{ number_format($payment->amount, 2) }}</p>
                    @if($monthCount > 1)
                        <p class="text-xs text-[#6B7280]">
                            {{ number_format($payment->amount / $monthCount, 2) }} per month
                        </p>
                    @endif
                </div>
                <div>
                    <p class="text-[#6B7280] text-xs mb-0.5">Status</p>
                    @php
                        $statusClasses = [
                            'Approved' => 'bg-green-600 text-white',
                            'Rejected' => 'bg-red-600 text-white',
                            'Pending' => 'bg-[#ca0251] text-white'
                        ];
                    @endphp
                    <span class="{{ $statusClasses[$payment->status] ?? 'bg-[#F3F4F6] text-[#374151]' }} px-2 py-0.5 rounded-full text-xs font-medium inline-block">
                        {{ $payment->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Screenshot -->
        <div class="bg-white rounded-lg border border-[#E5E7EB] px-5 py-4 flex flex-col">
            <h3 class="font-semibold text-sm mb-2 text-[#111827]">Payment Screenshot</h3>
            @if($payment->screenshot)
                <img 
                    src="{{ asset('payments/'.$payment->screenshot) }}" 
                    class="rounded-lg border w-full h-32 object-cover cursor-pointer"
                    onclick="openModal('{{ asset('payments/'.$payment->screenshot) }}')">
                <button 
                    onclick="openModal('{{ asset('payments/'.$payment->screenshot) }}')"
                    class="mt-2 text-[#ca0251] hover:text-[#a80244] text-sm text-center transition">
                    Open Full Image
                </button>
            @else
                <div class="text-[#6B7280] text-sm flex-1 flex items-center justify-center">
                    No screenshot uploaded.
                </div>
            @endif
        </div>

    </div>

    <!-- Action Tabs - Only show if Pending -->
    @if($payment->status == 'Pending')
        <div class="bg-white rounded-lg border border-[#E5E7EB] overflow-hidden" x-data="{ activeTab: 'approve' }">
            <!-- Tab Headers -->
            <div class="flex border-b border-[#E5E7EB]">
                <button 
                    @click="activeTab = 'approve'"
                    class="flex-1 px-4 py-3 text-sm font-medium transition-all duration-200"
                    :class="activeTab === 'approve' 
                        ? 'bg-[#ca0251] text-white' 
                        : 'text-[#374151] hover:bg-[#ca0251]/10 hover:text-[#ca0251]'">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve Payment
                    </span>
                </button>
                <button 
                    @click="activeTab = 'reject'"
                    class="flex-1 px-4 py-3 text-sm font-medium transition-all duration-200 relative group"
                    :class="activeTab === 'reject' 
                        ? 'bg-[#ca0251] text-white' 
                        : 'text-[#374151] hover:bg-[#ca0251]/10 hover:text-[#ca0251]'">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reject Payment
                    </span>
                    <!-- Red Tooltip -->
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-4 py-2 bg-red-600 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50 shadow-lg border border-red-700 max-w-xs">
                        ⚠️ Once you reject this tenant, contact them on why you reject
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1 border-4 border-transparent border-t-red-600"></div>
                    </div>
                </button>
            </div>

            <!-- Tab Content -->
            <div class="p-5">
                <!-- Approve Tab -->
                <div x-show="activeTab === 'approve'" x-transition:enter.duration.200ms>
                    <div class="space-y-3">
                        <p class="text-sm text-[#6B7280]">
                            Approve this payment to confirm it has been received successfully.
                        </p>
                        <form method="POST" action="{{ route('landlord.payments.approve', $payment) }}">
                            @csrf @method('PATCH')
                            <button class="bg-[#ca0251] hover:bg-[#a80244] text-white px-6 py-2.5 rounded-lg text-sm transition w-full sm:w-auto">
                                Confirm Approval
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Reject Tab -->
                <div x-show="activeTab === 'reject'" x-transition:enter.duration.200ms>
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('landlord.payments.reject', $payment) }}">
                            @csrf @method('PATCH')
                            <button class="bg-[#ca0251] hover:bg-[#a80244] text-white px-6 py-2.5 rounded-lg text-sm transition w-full sm:w-auto">
                                Confirm Rejection
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Decision Card - When not pending -->
        <div class="bg-white rounded-lg border border-[#E5E7EB] px-5 py-4">
            <h3 class="font-semibold text-sm mb-2 text-[#111827]">Decision</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-[#6B7280] text-xs mb-0.5">Approved By</p>
                    <p class="text-[#111827]">{{ optional($payment->approver)->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[#6B7280] text-xs mb-0.5">Date</p>
                    <p class="text-[#111827]">{{ optional($payment->approved_at)?->format('d M Y H:i') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[#6B7280] text-xs mb-0.5">Remarks</p>
                    <p class="text-[#111827]">{{ $payment->remarks ?: 'None' }}</p>
                </div>
            </div>
        </div>
    @endif

</div>

<!-- Centered Image Modal -->
<div id="imageModal" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm hidden" 
     onclick="closeModal()">
    <div class="relative max-w-[90vw] max-h-[90vh] flex items-center justify-center" onclick="event.stopPropagation()">
        <img 
            id="modalImage" 
            src="" 
            class="max-w-[90vw] max-h-[85vh] object-contain rounded-lg shadow-2xl border border-white/10"
            alt="Payment Screenshot">
        <button 
            onclick="closeModal()" 
            class="absolute top-4 right-4 text-white hover:text-gray-300 transition text-2xl bg-black/50 hover:bg-black/70 rounded-full w-10 h-10 flex items-center justify-center">
            ✕
        </button>
    </div>
</div>

<script>
    function openModal(src) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('modalImage');
        img.src = src;
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>

<style>
    /* Smooth modal animation */
    #imageModal {
        animation: modalFadeIn 0.3s ease-out;
    }
    
    #imageModal img {
        animation: imageZoomIn 0.3s ease-out;
    }
    
    @keyframes modalFadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes imageZoomIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

@endsection