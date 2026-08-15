<!-- resources/views/landlord/partials/header.blade.php -->

<header class="flex flex-shrink-0 items-center justify-between border-b border-[#a80244] bg-[#ca0251] py-3 pl-14 pr-4 sm:pr-6 lg:px-8">

    <!-- User Avatar with Username - Pushed to the right -->
    <div class="ml-auto flex items-center gap-3">
        <!-- Avatar with Heroicon -->
        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-white/20">
            <x-heroicon-o-user class="w-5 h-5 text-white" />
        </div>

        <!-- Username -->
        <span class="hidden text-sm font-medium text-white sm:inline">
            {{ auth()->user()->name }}
        </span>
    </div>

</header>
