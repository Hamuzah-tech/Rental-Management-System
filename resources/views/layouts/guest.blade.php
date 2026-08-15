<!-- resources/views/layouts/guest.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Alendi')</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-[#F8FAFC]">

    <div class="min-h-screen flex flex-col">

        <!-- Header (Optional - if you have a header for guest pages) -->
        @if(View::hasSection('header'))
            <header class="bg-[#ca0251] border-b border-[#a80244] px-4 sm:px-6 lg:px-8 py-3 flex-shrink-0">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-xl font-bold text-white">
                        @yield('header-title', 'Alendi')
                    </h1>
                </div>
            </header>
        @else
            <!-- Default header for guest pages -->
            <header class="bg-[#ca0251] border-b border-[#a80244] px-4 sm:px-6 lg:px-8 py-3 flex-shrink-0">
                <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
                    <h1 class="text-lg sm:text-xl font-bold text-white">Alendi</h1>
                    <p class="hidden sm:block text-sm text-white/80">For Landlords. For Tenants.</p>
                </div>
            </header>
        @endif

        <!-- Main Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-[#E5E7EB] flex-shrink-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p class="text-xs text-[#6B7280]">
                        &copy; {{ date('Y') }} <span class="font-medium text-[#111827]">Alendi</span>. All rights reserved.
                    </p>
                    <div class="flex items-center gap-1 text-xs text-[#6B7280]">
                        <span>Need help?</span>
                        <a href="https://wa.me/265990705194?text=Hi%2C%20I%20need%20help%20with%20Alendi" 
                           target="_blank"
                           rel="noopener noreferrer"
                           class="font-medium text-[#ca0251] hover:text-[#a80244] transition hover:underline">
                            Say Hi
                        </a>
                    </div>
                </div>
            </div>
        </footer>

    </div>

    @stack('scripts')

</body>
</html>