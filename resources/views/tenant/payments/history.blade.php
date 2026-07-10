<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment History</title>

@vite(['resources/css/app.css','resources/js/app.js'])

</head>


<body class="bg-white min-h-screen flex items-center justify-center px-6">



<div class="w-full max-w-xl">



    <!-- Back -->

    <div class="mb-6">

        <a href="{{ route('tenant.payments.index') }}"
           class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition">


            <x-heroicon-o-arrow-left
                class="w-5 h-5"/>


            <span>
                Back
            </span>


        </a>


    </div>






    <div class="bg-white rounded-xl border border-slate-200 p-8">





        <!-- Header -->


        <div class="mb-8">


            <div class="flex items-center gap-4 mb-5">


                <x-heroicon-o-document-text
                    class="w-12 h-12 text-slate-300"/>



                <div>


                    <h1 class="text-3xl font-bold text-slate-800">

                        Payment History

                    </h1>


                    <p class="text-sm text-slate-500 mt-1">

                        Enter your tenant code to view payment records.

                    </p>


                </div>



            </div>



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

                    value="{{ old('tenant_code') }}"

                    required

                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-slate-400"

                >


            </div>







            <button

                type="submit"

                class="mt-6 w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-3 rounded-xl transition">


                View History


            </button>





        </form>





    </div>





</div>



</body>

</html>