<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">

        <div class="text-center mb-8">

            <h1 class="text-3xl font-bold text-green-600">
                Rental Management System
            </h1>

            <p class="text-gray-500 mt-2">
                Landlord Portal
            </p>

        </div>

        @if ($errors->any())

            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 rounded-lg p-3">

                {{ $errors->first() }}

            </div>

        @endif

        <form method="POST" action="{{ route('landlord.login') }}">

            @csrf

            <div class="mb-4">

                <label class="block mb-2 font-medium">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500">

            </div>

            <div class="mb-6">

                <label class="block mb-2 font-medium">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500">

            </div>

            <div class="mb-6 flex items-center">

                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">

                <label for="remember" class="ml-2 text-sm text-gray-600">
                    Remember me
                </label>

            </div>

            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg transition">

                Login

            </button>

        </form>

    </div>

</div>

</body>
</html>