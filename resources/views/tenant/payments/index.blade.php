<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Portal</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-xl rounded-xl p-10 w-full max-w-3xl">

        <div class="text-center mb-10">

            <h1 class="text-4xl font-bold text-indigo-700">
                Rental Management System
            </h1>

            <p class="text-gray-500 mt-2">
                Tenant Self-Service Portal
            </p>

        </div>

        <div class="grid md:grid-cols-3 gap-6">

            <a href="{{ route('tenant.payments.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl p-8 text-center transition">

                <div class="text-5xl mb-4">
                    💳
                </div>

                <h2 class="font-bold text-xl">
                    Record Payment
                </h2>

                <p class="mt-2 text-sm">
                    Submit your rent payment.
                </p>

            </a>

            <a href="{{ route('tenant.payments.history') }}"
               class="bg-green-600 hover:bg-green-700 text-white rounded-xl p-8 text-center transition">

                <div class="text-5xl mb-4">
                    📄
                </div>

                <h2 class="font-bold text-xl">
                    Payment History
                </h2>

                <p class="mt-2 text-sm">
                    View your payment records.
                </p>

            </a>

            <a href="#"
               class="bg-orange-600 hover:bg-orange-700 text-white rounded-xl p-8 text-center transition">

                <div class="text-5xl mb-4">
                    🚪
                </div>

                <h2 class="font-bold text-xl">
                    Move-Out Notice
                </h2>

                <p class="mt-2 text-sm">
                    Submit your move-out request.
                </p>

            </a>

        </div>

    </div>

</div>

</body>
</html>