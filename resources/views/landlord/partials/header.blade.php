<!-- resources/views/landlord/partials/header.blade.php -->

<div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">

    <button
        type="button"
        class="lg:hidden p-2 -ml-2 rounded-lg text-white"
        @click="sidebarOpen = true"
        x-show="!sidebarOpen"
        aria-label="Open menu"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h14"/>
        </svg>
    </button>

    <div class="ml-auto flex items-center gap-3">
        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-white/20">
            <x-heroicon-o-user class="w-5 h-5 text-white" />
        </div>

        <span class="hidden text-sm font-medium text-white sm:inline">
            {{ auth()->user()->name }}
        </span>
    </div>

</div>
