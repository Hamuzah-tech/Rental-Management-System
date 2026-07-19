<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Landlord</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white">

<div class="min-h-screen flex items-center justify-center px-6">

    <div class="w-full max-w-md">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800">
                Reset Password
            </h1>

            <p class="text-slate-500 mt-2">
                Enter your new password.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('landlord.password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-slate-700">
                    Email Address
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ $email ?? old('email') }}"
                    required
                    readonly
                    class="w-full border border-slate-200 rounded-xl p-3 text-slate-700 bg-slate-50 outline-none">
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-slate-700">
                    New Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    autofocus
                    class="w-full border border-slate-200 rounded-xl p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-slate-700">
                    Confirm New Password
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    class="w-full border border-slate-200 rounded-xl p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none">
            </div>

            <button
                type="submit"
                class="w-full bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl transition">
                Reset Password
            </button>

        </form>

    </div>

</div>

</body>
</html>