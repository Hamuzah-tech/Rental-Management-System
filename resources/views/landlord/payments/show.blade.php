@extends('layouts.landlord')

@section('title','Payment Details')

@section('page-title','Payment Details')

@section('content')

<div class="space-y-6">

@if(session('success'))

    <div class="bg-green-100 text-green-800 px-5 py-3 rounded-lg">

        {{ session('success') }}

    </div>

@endif


<div class="bg-white rounded-xl shadow p-6">

    <div class="flex justify-between items-start">

        <div>

            <h2 class="text-2xl font-bold">
                Payment Information
            </h2>

            <p class="text-gray-500">
                Review this tenant payment.
            </p>

        </div>

        <a
            href="{{ route('landlord.payments.index') }}"
            class="text-indigo-600 hover:underline">

            ← Back to Payments

        </a>

    </div>

</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Payment Details -->

    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="font-bold text-lg mb-5">
            Payment Details
        </h3>

        <div class="space-y-4">

            <div>
                <strong>Tenant</strong><br>
                {{ $payment->tenant->name }}
            </div>

            <div>
                <strong>Tenant Code</strong><br>
                {{ $payment->tenant->tenant_code }}
            </div>

            <div>
                <strong>Property</strong><br>
                {{ $payment->tenant->property->name }}
            </div>

            <div>
                <strong>Payment Month</strong><br>
                {{ $payment->payment_month }}
            </div>

            <div>
                <strong>Amount Paid</strong><br>
                MK {{ number_format($payment->amount,2) }}
            </div>

            <div>

                <strong>Status</strong><br>

                @if($payment->status=='Approved')

                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">

                        Approved

                    </span>

                @elseif($payment->status=='Rejected')

                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">

                        Rejected

                    </span>

                @else

                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">

                        Pending

                    </span>

                @endif

            </div>

        </div>

    </div>


    <!-- Screenshot -->

    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="font-bold text-lg mb-5">

            Payment Screenshot

        </h3>

        @if($payment->screenshot)

            <img
                src="{{ asset('storage/'.$payment->screenshot) }}"
                class="rounded-lg border w-full">

            <a
                href="{{ asset('storage/'.$payment->screenshot) }}"
                target="_blank"
                class="inline-block mt-4 text-indigo-600 hover:underline">

                Open Full Image

            </a>

        @else

            <div class="text-gray-500">

                No screenshot uploaded.

            </div>

        @endif

    </div>

</div>


@if($payment->status=='Pending')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Approve -->

        <div class="bg-white rounded-xl shadow p-6">

            <h3 class="font-bold mb-4">

                Approve Payment

            </h3>

            <form
                method="POST"
                action="{{ route('landlord.payments.approve',$payment) }}">

                @csrf

                @method('PATCH')

                <button
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg">

                    Approve Payment

                </button>

            </form>

        </div>


        <!-- Reject -->

        <div class="bg-white rounded-xl shadow p-6">

            <h3 class="font-bold mb-4">

                Reject Payment

            </h3>

            <form
                method="POST"
                action="{{ route('landlord.payments.reject',$payment) }}">

                @csrf

                @method('PATCH')

                <textarea
                    name="remarks"
                    rows="4"
                    class="w-full border rounded-lg p-3"
                    placeholder="Reason for rejection (optional)"></textarea>

                <button
                    class="mt-4 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg">

                    Reject Payment

                </button>

            </form>

        </div>

    </div>

@else

    <div class="bg-white rounded-xl shadow p-6">

        <h3 class="font-bold mb-4">

            Decision

        </h3>

        <div class="space-y-3">

            <div>

                <strong>Approved By:</strong>

                {{ optional($payment->approver)->name ?? 'N/A' }}

            </div>

            <div>

                <strong>Date:</strong>

                {{ optional($payment->approved_at)?->format('d M Y H:i') ?? 'N/A' }}

            </div>

            <div>

                <strong>Remarks:</strong>

                {{ $payment->remarks ?: 'None' }}

            </div>

        </div>

    </div>

@endif

</div>

@endsection
