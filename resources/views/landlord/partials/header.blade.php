<!-- resources/views/landlord/partials/header.blade.php -->

<header class="bg-[#C80B6D] border-b border-[#a8095e] px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 flex-shrink-0">
    
    <!-- Page Title -->
    <h2 class="text-lg font-semibold text-white">
        @yield('page-title', 'Dashboard')
    </h2>

    <!-- User Avatar with Username -->
    <div class="flex items-center gap-3">
        <!-- Avatar with Heroicon -->
        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
            <x-heroicon-o-user class="w-5 h-5 text-white" />
        </div>
        
        <!-- Username -->
        <span class="text-sm font-medium text-white">
            {{ auth()->user()->name }}
        </span>
    </div>

</header>