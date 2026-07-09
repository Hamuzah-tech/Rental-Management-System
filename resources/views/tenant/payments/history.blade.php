<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment History</title>

@vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center">


<div class="w-full max-w-xl bg-white rounded-2xl shadow-lg border border-slate-200 p-8">


    <!-- Header -->
    <div class="text-center mb-8">

        <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-8 h-8 text-green-600"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h3a2 2 0 012 2v12a2 2 0 01-2 2z" />

            </svg>

        </div>


        <h1 class="text-3xl font-bold text-slate-800">

            Payment History

        </h1>


        <p class="text-sm text-slate-500 mt-2">

            Enter your tenant code to view previous payment records.

        </p>

    </div>



    <!-- Errors -->
    @if ($errors->any())

        <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-4 text-red-700 text-sm">

            {{ $errors->first() }}

        </div>

    @endif



    <!-- Search Form -->
    <form method="POST"
          action="{{ route('tenant.payments.search') }}">

        @csrf


        <div>

            <label class="block text-sm font-semibold text-slate-700 mb-2">

                Tenant Code

            </label>


            <input
                name="tenant_code"
                required
                placeholder="Enter your tenant code"
                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
            >

        </div>



        <button
            type="submit"
            class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition shadow">

            View History

        </button>


    </form>



    <!-- Exit Button -->
    <button
        onclick="exitPortal()"
        class="mt-4 w-full border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold py-3 rounded-xl transition">

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