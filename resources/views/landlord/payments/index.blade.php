@extends('layouts.landlord')

@section('title','Payments')

@section('page-title','Payments')

@section('content')

<div class="space-y-6">

<div class="bg-white rounded-xl shadow p-6">

    <div class="flex items-center justify-between">

        <div>

            <h2 class="text-2xl font-bold">
                Tenant Payments
            </h2>

            <p class="text-gray-500">
                Review and approve tenant payments.
            </p>

        </div>

        <form method="GET">

            <select
                name="status"
                onchange="this.form.submit()"
                class="border rounded-lg p-2">

                <option value="">
                    All Payments
                </option>

                <option value="Pending"
                    @selected(request('status')=='Pending')>
                    Pending
                </option>

                <option value="Approved"
                    @selected(request('status')=='Approved')>
                    Approved
                </option>

                <option value="Rejected"
                    @selected(request('status')=='Rejected')>
                    Rejected
                </option>

            </select>

        </form>

    </div>

</div>


@if(session('success'))

    <div class="bg-green-100 text-green-800 px-5 py-3 rounded-lg">

        {{ session('success') }}

    </div>

@endif


<div class="bg-white rounded-xl shadow overflow-hidden">

    <table class="w-full">

        <thead class="bg-gray-100">

            <tr>

                <th class="px-6 py-4 text-left">
                    Tenant
                </th>

                <th class="px-6 py-4 text-left">
                    Property
                </th>

                <th class="px-6 py-4 text-left">
                    Month
                </th>

                <th class="px-6 py-4 text-left">
                    Amount
                </th>

                <th class="px-6 py-4 text-left">
                    Status
                </th>

                <th class="px-6 py-4 text-center">
                    Action
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($payments as $payment)

            <tr class="border-t">

                <td class="px-6 py-4">

                    {{ $payment->tenant->name }}

                </td>

                <td class="px-6 py-4">

                    {{ $payment->tenant->property->name }}

                </td>

                <td class="px-6 py-4">

                    {{ $payment->payment_month }}

                </td>

                <td class="px-6 py-4">

                    MK {{ number_format($payment->amount) }}

                </td>

                <td class="px-6 py-4">

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

                </td>

                <td class="px-6 py-4 text-center">

                    <a
                        href="{{ route('landlord.payments.show',$payment) }}"
                        class="text-indigo-600 hover:underline">

                        View

                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td
                    colspan="6"
                    class="text-center py-8 text-gray-500">

                    No payments found.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

{{ $payments->links() }}
</div>

@endsection
