<!-- resources/views/layouts/landlord.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Landlord Portal')</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">

    <!-- Landlord Sidebar - Fixed -->
    @include('landlord.partials.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden lg:ml-[270px]">

        <!-- Header - Sticky -->
        <header class="flex-shrink-0 bg-[#C80B6D] border-b border-[#a8095e] z-40">
            @include('landlord.partials.header')
        </header>

        <!-- Page Content - Centered with max-width -->
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="flex-shrink-0 bg-white border-t border-[#E5E7EB]">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p class="text-xs text-[#6B7280]">
                        &copy; {{ date('Y') }} <span class="font-medium text-[#111827]">Alendi</span>. All rights reserved.
                    </p>
                    <div class="flex items-center gap-1 text-xs text-[#6B7280]">
                        <span>Need help?</span>
                        <a href="https://wa.me/0990705194?text=Hi%2C%20I%20need%20help%20with%20Alendi" 
                           target="_blank"
                           rel="noopener noreferrer"
                           class="font-medium text-[#0F172A] hover:text-[#C80B6D] transition hover:underline">
                            Click me
                        </a>
                    </div>
                </div>
            </div>
        </footer>

    </div>

</div>

<!-- Scripts Stack -->
@stack('scripts')

</body>
</html>