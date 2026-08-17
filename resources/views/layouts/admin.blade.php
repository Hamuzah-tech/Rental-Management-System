<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rental Management System')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
            width: 16rem;
            height: 100%;
            height: 100dvh;
        }

        @media (max-width: 1023px) {
            .app-sidebar {
                transform: translateX(-100%);
                pointer-events: none;
            }
            .app-sidebar.is-open {
                transform: translateX(0);
                pointer-events: auto;
            }
        }

        @media (min-width: 1024px) {
            .app-sidebar {
                transform: none !important;
                pointer-events: auto;
            }
            .app-content {
                margin-left: 16rem;
            }
        }
    </style>
</head>

<body class="bg-slate-100">

<div
    class="min-h-screen"
    x-data="{ sidebarOpen: false }"
    @keydown.escape.window="sidebarOpen = false"
>

    {{-- Mobile overlay --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/40 lg:hidden"
        x-cloak
    ></div>

    {{-- Sidebar is always overlay/fixed, never in the content flow --}}
    <x-admin.sidebar />

    <div class="app-content flex min-h-screen min-w-0 flex-col">

        {{-- Topbar --}}
        <x-admin.topbar />

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

    </div>

</div>

<x-toast-container />

</body>
</html>
