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
    <style>
        [x-cloak] { display: none !important; }
        @media (max-width: 1023px) {
            .app-sidebar { transform: translateX(-100%); }
            .app-sidebar.is-open { transform: translateX(0); }
        }
        @media (min-width: 1024px) {
            .app-sidebar { transform: translateX(0) !important; }
        }
    </style>
</head>

<body class="bg-gray-100">

<div
    class="flex h-screen overflow-hidden"
    x-data="{
        sidebarOpen: false,
        isDesktop: false,
        init() {
            const sync = () => {
                this.isDesktop = window.innerWidth >= 1024;
                if (this.isDesktop) this.sidebarOpen = false;
            };
            sync();
            window.addEventListener('resize', sync);
        }
    }"
    @keydown.escape.window="sidebarOpen = false"
>

    <!-- Landlord Sidebar - Fixed -->
    @include('landlord.partials.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden lg:ml-[270px] min-w-0">

        <!-- Header - Sticky -->
        <header class="flex-shrink-0 bg-[#ca0251] border-b border-[#a80244] z-40">
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

</div>

<!-- Scripts Stack -->
@stack('scripts')

</body>
</html>
