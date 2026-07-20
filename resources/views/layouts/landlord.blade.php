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

    <!-- Landlord Sidebar - Fixed (Reduced width) -->
    @include('landlord.partials.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden lg:ml-[270px]">

        <!-- Header - Sticky -->
        <header class="flex-shrink-0 bg-white border-b border-slate-200 z-40">
            @include('landlord.partials.header')
        </header>

        <!-- Page Content - Centered with max-width -->
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                @yield('content')
            </div>
        </main>

    </div>

</div>

<!-- Scripts Stack -->
@stack('scripts')

</body>
</html>