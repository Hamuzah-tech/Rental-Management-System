<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Super Admin Login</title>

    <!-- Tailwind via CDN (lightweight for demo) + Heroicons inline SVG -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* simple smooth transition for the icon */
        .toggle-pw {
            cursor: pointer;
            transition: opacity 0.15s ease;
        }
        .toggle-pw:hover {
            opacity: 0.7;
        }
    </style>
</head>
<body class="bg-white">

<div class="min-h-screen flex items-center justify-center px-6">

    <div class="w-full max-w-md">

        <!-- header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Portal</h1>
            <p class="text-slate-500 mt-2">Operations manager workspace login.</p>
        </div>

        <!-- demo error message (static) -->
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm hidden">
            <!-- hidden by default; only for demo -->
            Invalid credentials.
        </div>

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-slate-700">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full border border-slate-200 rounded-xl p-3 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none"
                />
            </div>

            <!-- Password with show/hide icon -->
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-slate-700">Password</label>

                <div class="relative">
                    <input
                        id="password-field"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full border border-slate-200 rounded-xl p-3 pr-12 text-slate-700 focus:ring-2 focus:ring-slate-400 focus:border-slate-400 outline-none"
                    />
                    <!-- Heroicon: Eye (show/hide) -->
                    <button
                        type="button"
                        id="togglePassword"
                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600 toggle-pw"
                        aria-label="Show password"
                    >
                        <!-- Eye icon (Heroicon outline) -->
                        <svg
                            id="eye-icon"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember me -->
            <div class="mb-6 flex items-center">
                <input
                    type="checkbox"
                    name="remember"
                    class="rounded border-slate-300 text-slate-600 focus:ring-slate-400"
                />
                <label class="ml-2 text-sm text-slate-500">Remember me</label>
            </div>

            <!-- Login button -->
            <button
                type="submit"
                class="w-full bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-xl transition"
            >
                Login
            </button>
        </form>

    </div>
</div>

<!-- minimal JS to toggle password visibility and swap Heroicon -->
<script>
    (function() {
        const toggleBtn = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password-field');
        const eyeIcon = document.getElementById('eye-icon');

        if (toggleBtn && passwordField && eyeIcon) {
            toggleBtn.addEventListener('click', function() {
                // toggle password type
                const isPassword = passwordField.type === 'password';
                passwordField.type = isPassword ? 'text' : 'password';

                // swap Heroicon: show "eye" (default) or "eye-slash"
                if (isPassword) {
                    // currently hidden → show password → switch to eye-slash
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    `;
                } else {
                    // currently visible → hide password → switch back to eye
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    `;
                }
            });
        }
    })();
</script>

</body>
</html>