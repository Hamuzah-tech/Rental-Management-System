@extends('layouts.landlord')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="space-y-4">

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white rounded-xl shadow p-4 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold">Payment Information</h2>
            <p class="text-gray-500 text-sm">Review this tenant payment.</p>
        </div>
        <a href="{{ route('landlord.payments.index') }}" class="text-indigo-600 hover:underline text-sm">
            ← Back to Payments
        </a>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Payment Details -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-4">
            <h3 class="font-bold text-md mb-3">Payment Details</h3>
            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                <div><strong>Tenant</strong><br>{{ $payment->tenant->name }}</div>
                <div><strong>Tenant Code</strong><br>{{ $payment->tenant->tenant_code }}</div>
                <div><strong>Property</strong><br>{{ $payment->tenant->property->name }}</div>
                <div><strong>Payment Month</strong><br>{{ $payment->payment_month }}</div>
                <div><strong>Amount Paid</strong><br>MK {{ number_format($payment->amount, 2) }}</div>
                <div>
                    <strong>Status</strong><br>
                    @php
                        $statusClasses = [
                            'Approved' => 'bg-green-100 text-green-700',
                            'Rejected' => 'bg-red-100 text-red-700',
                            'Pending' => 'bg-yellow-100 text-yellow-700'
                        ];
                    @endphp
                    <span class="{{ $statusClasses[$payment->status] ?? 'bg-gray-100 text-gray-700' }} px-2 py-0.5 rounded-full text-xs font-medium">
                        {{ $payment->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Screenshot -->
        <div class="bg-white rounded-xl shadow p-4 flex flex-col">
            <h3 class="font-bold text-md mb-2">Payment Screenshot</h3>
            @if($payment->screenshot)
                <img 
                    src="{{ asset('storage/'.$payment->screenshot) }}" 
                    class="rounded-lg border w-full h-32 object-cover cursor-pointer"
                    onclick="openModal('{{ asset('storage/'.$payment->screenshot) }}')">
                <button 
                    onclick="openModal('{{ asset('storage/'.$payment->screenshot) }}')"
                    class="mt-2 text-indigo-600 hover:underline text-sm text-center">
                    Open Full Image
                </button>
            @else
                <div class="text-gray-500 text-sm flex-1 flex items-center justify-center">
                    No screenshot uploaded.
                </div>
            @endif
        </div>

    </div>

    <!-- Action Section -->
    @if($payment->status == 'Pending')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl shadow p-4">
                <h3 class="font-bold text-md mb-2">Approve Payment</h3>
                <form method="POST" action="{{ route('landlord.payments.approve', $payment) }}">
                    @csrf @method('PATCH')
                    <button class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm w-full">
                        Approve Payment
                    </button>
                </form>
            </div>
            <div class="bg-white rounded-xl shadow p-4">
                <h3 class="font-bold text-md mb-2">Reject Payment</h3>
                <form method="POST" action="{{ route('landlord.payments.reject', $payment) }}">
                    @csrf @method('PATCH')
                    <textarea name="remarks" rows="2" class="w-full border rounded-lg p-2 text-sm" placeholder="Reason (optional)"></textarea>
                    <button class="mt-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm w-full">
                        Reject Payment
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="font-bold text-md mb-2">Decision</h3>
            <div class="grid grid-cols-3 gap-2 text-sm">
                <div><strong>Approved By:</strong><br>{{ optional($payment->approver)->name ?? 'N/A' }}</div>
                <div><strong>Date:</strong><br>{{ optional($payment->approved_at)?->format('d M Y H:i') ?? 'N/A' }}</div>
                <div><strong>Remarks:</strong><br>{{ $payment->remarks ?: 'None' }}</div>
            </div>
        </div>
    @endif

</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 items-center justify-center z-50 hidden" onclick="closeModal()">
    <div class="relative max-w-4xl max-h-[90vh]" onclick="event.stopPropagation()">
        <img id="modalImage" src="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl">
        <button onclick="closeModal()" class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full w-8 h-8 flex items-center justify-center hover:bg-opacity-75 text-xl">✕</button>
    </div>
</div>

<script>
    function openModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endsection