<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100">

<div class="min-h-screen flex items-center justify-center">

    <div class="max-w-5xl w-full px-6">

        <div class="text-center mb-12">

            <h1 class="text-5xl font-bold text-slate-800">
                Rental Management System
            </h1>

            <p class="mt-4 text-lg text-slate-500">
                Select the portal you want to access.
            </p>

        </div>


        <div class="grid md:grid-cols-2 gap-8">


            <!-- Staff -->

            <a href="{{ route('admin.login') }}"
               class="bg-white rounded-2xl shadow-lg p-10 hover:shadow-2xl transition text-center">

                <div class="flex justify-center mb-6">

                    <x-heroicon-o-user-circle class="w-20 h-20 text-indigo-600"/>

                </div>

                <h2 class="text-2xl font-bold text-slate-800">
                    Staff Portal
                </h2>

                <p class="mt-3 text-gray-500">
                    Super Administrator and Staff Login
                </p>

                <div class="mt-8">

                    <span class="bg-indigo-600 text-white px-6 py-3 rounded-lg">
                        Login
                    </span>

                </div>

            </a>



            <!-- Landlord -->

            <a href="{{ route('landlord.login') }}"
               class="bg-white rounded-2xl shadow-lg p-10 hover:shadow-2xl transition text-center">

                <div class="flex justify-center mb-6">

                    <x-heroicon-o-home-modern class="w-20 h-20 text-green-600"/>

                </div>

                <h2 class="text-2xl font-bold text-slate-800">
                    Landlord Portal
                </h2>

                <p class="mt-3 text-gray-500">
                    Login to manage your properties and tenants.
                </p>

                <div class="mt-8">

                    <span class="bg-green-600 text-white px-6 py-3 rounded-lg">
                        Login
                    </span>

                </div>

            </a>

        </div>

    </div>

</div>

</body>
</html>