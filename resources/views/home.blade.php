<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
<div class="min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-md">
        <h1 class="text-3xl font-bold text-slate-800">
           Welcome
        </h1>
        <p class="mt-2 text-slate-500">
            Choose your workspace to continue.
        </p>
        <div class="mt-10 space-y-4">
            <!-- Landlord -->
            <a href="{{ route('landlord.login') }}"
               class="flex items-center gap-4 rounded-xl border border-slate-200 p-5 transition hover:border-slate-400">

                <x-heroicon-o-home-modern
                    class="w-12 h-12 text-slate-300 flex-shrink-0"/>

                <div class="flex-1">
                    <h2 class="font-semibold text-slate-800">
                        Landlord 
                    </h2>
                </div>

                <span class="text-slate-400 text-xl">
                    →
                </span>

            </a>

            <!-- Tenant Portal -->
            <a href="{{ route('tenant.payments.index') }}"
               class="flex items-center gap-4 rounded-xl border border-slate-200 p-5 transition hover:border-slate-400">

                <x-heroicon-o-credit-card
                    class="w-12 h-12 text-slate-300 flex-shrink-0"/>

                <div class="flex-1">
                    <h2 class="font-semibold text-slate-800">
                        Tenant 
                    </h2>
                </div>

                <span class="text-slate-400 text-xl">
                    →
                </span>

            </a>

        </div>

    </div>

</div>

</body>
</html>