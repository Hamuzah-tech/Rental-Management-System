<!-- resources/views/landlord/partials/header.blade.php -->

<header class="bg-[#ca0251] border-b border-[#a80244] px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between flex-shrink-0">
    
    <!-- User Avatar with Username - Pushed to the right -->
    <div class="flex items-center gap-3 ml-auto">
        <!-- Avatar with Heroicon -->
        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
            <x-heroicon-o-user class="w-5 h-5 text-white" />
        </div>
        
        <!-- Username -->
        <span class="text-sm font-medium text-white hidden sm:inline">
            {{ auth()->user()->name }}
        </span>
    </div>

</header>