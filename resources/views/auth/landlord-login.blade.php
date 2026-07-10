<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white">

<div class="min-h-screen flex items-center justify-center px-6">

    <div class="w-full max-w-md">

        <div class="mb-8">

            <h1 class="text-3xl font-bold text-slate-800">
                Landlord
            </h1>

            <p class="text-slate-500 mt-2">
                Landlord workspace login.
            </p>

        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('landlord.login') }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-slate-700">
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full border border-slate-200 rounded-xl p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-slate-700">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full border border-slate-200 rounded-xl p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">
            </div>

            <button
                type="submit"
                class="w-full bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl transition">
                Login
            </button>

        </form>

        <!-- Forgot Password Link -->
        <div class="mt-4 text-center">
            <a href="{{ route('landlord.password.request') }}" 
               class="text-sm text-slate-500 hover:text-slate-700 transition">
                Forgot your password?
            </a>
        </div>

    </div>

</div>

</body>
</html>