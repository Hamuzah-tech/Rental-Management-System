<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="bg-white">


<div class="min-h-screen flex items-center justify-center px-6">


    <div class="w-full max-w-3xl">

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition">
                <x-heroicon-o-arrow-left
                    class="w-5 h-5"/>
                <span>Back</span>
            </a>
        </div>

        <h1 class="text-3xl font-bold text-slate-800">
            Tenant
        </h1>


        <p class="mt-2 text-slate-500">
            Manage your rent payments and move-out requests.
        </p>




        <div class="mt-10 space-y-4">



            <!-- Record Payment -->

            <a href="{{ route('tenant.payments.create') }}"
               class="flex items-center gap-4 rounded-xl border border-slate-200 p-5 transition hover:border-slate-400">


                <x-heroicon-o-credit-card
                    class="w-12 h-12 text-slate-300 flex-shrink-0"/>


                <div class="flex-1">


                    <h2 class="font-semibold text-slate-800">
                        Record Payment
                    </h2>


                    <p class="text-sm text-slate-500">
                        Submit your rent payment with proof.
                    </p>


                </div>



                <span class="text-slate-400 text-xl">
                    →
                </span>


            </a>







            <!-- Payment History -->


            <a href="{{ route('tenant.payments.history') }}"
               class="flex items-center gap-4 rounded-xl border border-slate-200 p-5 transition hover:border-slate-400">



                <x-heroicon-o-document-text
                    class="w-12 h-12 text-slate-300 flex-shrink-0"/>



                <div class="flex-1">


                    <h2 class="font-semibold text-slate-800">
                        Payment History
                    </h2>


                    <p class="text-sm text-slate-500">
                        Check approved, pending and rejected payments.
                    </p>


                </div>



                <span class="text-slate-400 text-xl">
                    →
                </span>



            </a>









            <!-- Move Out Notice -->


            <a href="{{ route('tenant.moveout.create') }}"
               class="flex items-center gap-4 rounded-xl border border-slate-200 p-5 transition hover:border-slate-400">



                <x-heroicon-o-arrow-right-on-rectangle
                    class="w-12 h-12 text-slate-300 flex-shrink-0"/>



                <div class="flex-1">


                    <h2 class="font-semibold text-slate-800">
                        Move-Out Notice
                    </h2>


                    <p class="text-sm text-slate-500">
                        Submit a request to leave your property.
                    </p>


                </div>



                <span class="text-slate-400 text-xl">
                    →
                </span>



            </a>





        </div>



    </div>



</div>



</body>
</html>