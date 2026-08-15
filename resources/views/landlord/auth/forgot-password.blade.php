<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Alendi Estates</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white">

<div class="min-h-screen flex items-center justify-center px-4 sm:px-6">

    <div class="w-full max-w-md">

        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/alendi_logo.jpg') }}" 
                 alt="Alendi Estates" 
                 class="h-14 w-auto">
        </div>

        <!-- Title -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
                Reset Password
            </h1>
            <p class="text-slate-500 mt-2 text-sm">
                Enter your email address and we'll send you a link to reset your password.
            </p>
        </div>

        <!-- Success Message -->
        @if (session('status'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-600 rounded-lg p-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- 
            IMPORTANT: This form MUST use method="POST" 
            and action MUST point to route('landlord.password.email')
        -->
        <form method="POST" action="{{ route('landlord.password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block mb-1 text-sm font-medium text-slate-700">
                    Email Address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full border border-slate-200 rounded-lg p-2.5 text-slate-700 focus:ring-2 focus:ring-[#ca0251] focus:border-[#ca0251] outline-none text-sm"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-[#ca0251] hover:bg-[#a80244] text-white py-2.5 rounded-lg transition text-sm font-medium">
                Send Password Reset Link
            </button>

        </form>

        <!-- Links -->
        <div class="mt-6 text-center">
            <a href="{{ route('landlord.login') }}" 
               class="text-sm text-slate-500 hover:text-[#ca0251] transition">
                Back to Login
            </a>
        </div>

    </div>

</div>

</body>
</html>