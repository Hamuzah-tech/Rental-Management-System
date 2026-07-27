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
        
        <!-- Logo -->
        <div class="flex justify-center mb-4">
            @php
                $logoPath = public_path('images/alendi_logo.jpg');
                $logoUrl = file_exists($logoPath) ? asset('images/alendi_logo.jpg') : 'https://via.placeholder.com/150x50?text=Alendi';
            @endphp
            <img src="{{ $logoUrl }}" 
                 alt="Alendi Logo" 
                 class="h-14 w-auto">
        </div>

        <!-- Centered Welcome Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[#111827]">
                Welcome
            </h1>
            <p class="mt-2 text-[#6B7280]">
                Choose your workspace to continue.
            </p>
        </div>

        <div class="mt-10 space-y-4">
            <!-- Landlord -->
            <a href="{{ route('landlord.login') }}"
               class="flex items-center gap-4 rounded-xl border border-[#E5E7EB] p-5 transition hover:border-[#0F172A] hover:shadow-sm">

                <x-heroicon-o-home-modern
                    class="w-12 h-12 text-[#6B7280] flex-shrink-0"/>

                <div class="flex-1">
                    <h2 class="font-semibold text-[#111827]">
                        Landlord 
                    </h2>
                </div>

                <span class="text-[#6B7280] text-xl">
                    →
                </span>

            </a>

            <!-- Tenant Portal -->
            <a href="{{ route('tenant.payments.index') }}"
               class="flex items-center gap-4 rounded-xl border border-[#E5E7EB] p-5 transition hover:border-[#0F172A] hover:shadow-sm">

                <x-heroicon-o-credit-card
                    class="w-12 h-12 text-[#6B7280] flex-shrink-0"/>

                <div class="flex-1">
                    <h2 class="font-semibold text-[#111827]">
                        Tenant 
                    </h2>
                </div>

                <span class="text-[#6B7280] text-xl">
                    →
                </span>

            </a>

            <!-- User Manual Section -->
            <div class="relative group">
                <button class="w-full flex items-center gap-4 rounded-xl border border-[#E5E7EB] p-5 transition hover:border-[#0F172A] hover:shadow-sm cursor-pointer">
                    <svg class="w-12 h-12 text-[#6B7280] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <div class="flex-1 text-left">
                        <h2 class="font-semibold text-[#111827]">
                            User Manual
                        </h2>
                        <p class="text-sm text-[#6B7280]">Download user guides</p>
                    </div>
                    <svg class="w-5 h-5 text-[#6B7280] transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div class="absolute left-0 right-0 mt-1 bg-white rounded-xl border border-[#E5E7EB] shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10 overflow-hidden">
                    <a href="{{ asset('storage/manuals/landlord-manual.pdf') }}" 
                       target="_blank"
                       download
                       class="flex items-center gap-3 px-5 py-3 hover:bg-[#F3F4F6] transition border-b border-[#E5E7EB] last:border-0">
                        <x-heroicon-o-user-circle class="w-5 h-5 text-[#6B7280]"/>
                        <span class="text-sm text-[#111827]">Landlord Manual</span>
                        <svg class="w-4 h-4 text-[#6B7280] ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                    <a href="{{ asset('storage/manuals/tenant-manual.pdf') }}" 
                       target="_blank"
                       download
                       class="flex items-center gap-3 px-5 py-3 hover:bg-[#F3F4F6] transition border-b border-[#E5E7EB] last:border-0">
                        <x-heroicon-o-user class="w-5 h-5 text-[#6B7280]"/>
                        <span class="text-sm text-[#111827]">Tenant Manual</span>
                        <svg class="w-4 h-4 text-[#6B7280] ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

<style>
    .group:hover .group-hover\:rotate-180 {
        transform: rotate(180deg);
    }
</style>

</body>
</html>