<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rental Management System')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <x-admin.sidebar />

    <div class="flex-1 flex flex-col">

        {{-- Topbar --}}
        <x-admin.topbar />

        <main class="flex-1 p-8">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>