<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Landlord</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white">

<div class="min-h-screen flex items-center justify-center px-6">

    <div class="w-full max-w-md">

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('landlord.login') }}"
               class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 transition">
                <x-heroicon-o-arrow-left class="w-5 h-5"/>
                <span>Back to Login</span>
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800">
                Reset Password
            </h1>

            <p class="text-slate-500 mt-2">
                Enter your email address and we'll send you a link to reset your password.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-600 rounded-lg p-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('landlord.password.email') }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-slate-700">
                    Email Address
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full border border-slate-200 rounded-xl p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">
            </div>

            <button
                type="submit"
                class="w-full bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl transition">
                Send Reset Link
            </button>

        </form>

    </div>

</div>

</body>
</html>