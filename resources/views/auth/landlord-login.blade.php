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

        <!-- Logo -->
        <div class="flex justify-center mb-2">
            <img src="{{ asset('images/alendi_logo.jpg') }}" 
                 alt="Alendi Estates" 
                 class="h-14 w-auto">
        </div>

        <!-- Centered Title -->
        <div class="mb-6 text-center">
            
            <p class="text-slate-500 mt-1 text-sm">
                Landlord portal login.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Upright Form -->
        <form method="POST" action="{{ route('landlord.login') }}">
            @csrf

            <div class="mb-3">
                <label class="block mb-1 text-sm font-medium text-slate-700">
                    Username
                </label>
                <input
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full border border-slate-200 rounded-lg p-2.5 text-slate-700 focus:ring-2 focus:ring-[#ca0251] focus:border-[#ca0251] outline-none text-sm">
            </div>

            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-slate-700">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full border border-slate-200 rounded-lg p-2.5 text-slate-700 focus:ring-2 focus:ring-[#ca0251] focus:border-[#ca0251] outline-none text-sm">
            </div>

            <button
                type="submit"
                class="w-full bg-[#ca0251] hover:bg-[#a80244] text-white py-2.5 rounded-lg transition text-sm font-medium">
                Login
            </button>

        </form>

        <!-- Forgot Password & Home Links -->
        <div class="mt-4 flex justify-between items-center">
            <a href="{{ route('landlord.password.request') }}" 
               class="text-sm text-slate-500 hover:text-[#ca0251] transition">
                Forgot your password?
            </a>
            
            <a href="{{ route('home') }}"
               class="text-sm text-slate-500 hover:text-[#ca0251] transition">
                Home
            </a>
        </div>

    </div>

</div>

</body>
</html>