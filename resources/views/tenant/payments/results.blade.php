<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment History</title>

@vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-gray-100 min-h-screen">


<div class="max-w-5xl mx-auto mt-10 bg-white rounded-xl shadow p-8">


    <div class="flex justify-between items-center mb-6">

        <h1 class="text-3xl font-bold text-green-700">
            Payment History
        </h1>


        <button
            onclick="exitPortal()"
            class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg transition">

            Exit

        </button>

    </div>



    <div class="mt-6 mb-8 bg-gray-50 rounded-lg p-5 border border-gray-200">


        <p class="mb-2">

            <strong>Tenant:</strong>

            {{ $tenant->name }}

        </p>


        <p class="mb-2">

            <strong>Property:</strong>

            {{ $tenant->property->name }}

        </p>


        <p>

            <strong>Tenant Code:</strong>

            {{ $tenant->tenant_code }}

        </p>


    </div>



    <div class="overflow-x-auto">

        <table class="w-full border-collapse">


            <thead class="bg-gray-100">

            <tr>

                <th class="p-3 text-left text-gray-700">
                    Month
                </th>

                <th class="p-3 text-left text-gray-700">
                    Amount
                </th>

                <th class="p-3 text-left text-gray-700">
                    Status
                </th>

            </tr>

            </thead>


            <tbody>


            @forelse($payments as $payment)


            <tr class="border-t">


                <td class="p-3">

                    {{ $payment->payment_month }}

                </td>


                <td class="p-3">

                    MK {{ number_format($payment->amount,2) }}

                </td>


                <td class="p-3">


                    @if($payment->status == 'Approved')

                        <span class="text-green-700 font-semibold">

                            Approved

                        </span>


                    @elseif($payment->status == 'Rejected')

                        <span class="text-red-700 font-semibold">

                            Rejected

                        </span>


                    @else

                        <span class="text-yellow-700 font-semibold">

                            Pending

                        </span>


                    @endif


                </td>


            </tr>


            @empty


            <tr>

                <td colspan="3"
                    class="text-center py-6 text-gray-500">

                    No payment history found.

                </td>

            </tr>


            @endforelse


            </tbody>


        </table>

    </div>



    <button
        onclick="exitPortal()"
        class="mt-8 w-full border border-gray-300 hover:bg-gray-100 text-gray-700 font-semibold py-3 rounded-lg transition">

        Exit Portal

    </button>


</div>



<script>

function exitPortal()
{
    window.close();

    setTimeout(function(){

        window.location.href = "{{ route('home') }}";

    }, 300);
}

</script>


</body>
</html>