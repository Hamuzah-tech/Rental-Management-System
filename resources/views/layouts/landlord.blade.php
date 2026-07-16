<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Add CSRF token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title','Landlord Portal')
    </title>


    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body class="bg-gray-100">


<div class="flex min-h-screen">


    {{-- Landlord Sidebar --}}

    @include('landlord.partials.sidebar')



    <div class="flex-1">


        {{-- Header --}}

        @include('landlord.partials.header')



        <main class="p-6">


            @yield('content')


        </main>


    </div>


</div>

<!-- Scripts Stack -->
@stack('scripts')

</body>

</html>