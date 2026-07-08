<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-100">

<div class="min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">

        <div class="text-center mb-8">

            <h1 class="text-3xl font-bold text-slate-800">
                Rental MS
            </h1>

            <p class="text-gray-500 mt-2">
                Super Administrator Login
            </p>

        </div>

                @if ($errors->any())

        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 rounded-lg p-3">

            {{ $errors->first() }}

        </div>

        @endif

        <form method="POST" action="{{ route('admin.login') }}">

            @csrf

            <div class="mb-4">

                <label class="block mb-2 font-medium">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-6">

                <label class="block mb-2 font-medium">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="w-full border rounded-lg p-3">

            </div>

            <div class="mb-6">

                <label class="flex items-center">

                    <input
                        type="checkbox"
                        name="remember"
                        class="mr-2">

                    Remember me

                </label>

            </div>

            <button
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg">

                Login as Super Admin

            </button>

        </form>

    </div>

</div>

</body>
</html>