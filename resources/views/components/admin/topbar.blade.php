@php

use App\Models\Property;
use App\Models\Tenant;

$notifications = collect();

// Latest properties
foreach (Property::with('landlord')->latest()->take(5)->get() as $property) {

    $landlord = $property->landlord->name ?? 'A landlord';

    $notifications->push([
        'message' => $landlord . ' added property "' . $property->name . '"',
        'time' => $property->created_at,
    ]);
}

// Latest tenants
foreach (Tenant::with('property.landlord')->latest()->take(5)->get() as $tenant) {

    $landlord = $tenant->property->landlord->name ?? 'A landlord';

    $notifications->push([
        'message' => $landlord . ' registered tenant "' . $tenant->name . '"',
        'time' => $tenant->created_at,
    ]);
}

// Sort newest first
$notifications = $notifications
    ->sortByDesc('time')
    ->take(10)
    ->values();

$notificationCount = $notifications->count();

@endphp

<header class="flex h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">

    <div class="flex min-w-0 items-center gap-3">
        <button
            type="button"
            @click="sidebarOpen = true"
            class="inline-flex items-center justify-center rounded-lg p-2 text-slate-700 hover:bg-slate-100 lg:hidden"
            aria-label="Open menu"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <h1 class="truncate text-lg font-semibold text-slate-800 sm:text-2xl">
            @yield('page-title', 'Dashboard')
        </h1>
    </div>

    <div class="flex flex-shrink-0 items-center gap-3 sm:gap-6">

        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">

            <button
                type="button"
                @click="open = !open"
                class="relative text-slate-500 transition hover:text-slate-700">

                <x-heroicon-o-bell class="w-6 h-6"/>

                @if($notificationCount > 0)
                    <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs text-white">
                        {{ $notificationCount }}
                    </span>
                @endif

            </button>

            <!-- Notification Dropdown -->
            <div
                x-show="open"
                x-cloak
                x-transition
                class="absolute right-0 z-50 mt-3 max-h-[70vh] w-[min(24rem,calc(100vw-2rem))] overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl">

                <div class="border-b px-4 py-3">
                    <h3 class="font-semibold text-slate-700">
                        Recent Activity
                    </h3>
                </div>

                @forelse($notifications as $notification)

                    <div class="border-b px-4 py-3 hover:bg-slate-50">

                        <p class="break-anywhere text-sm text-slate-700">
                            {{ $notification['message'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            {{ $notification['time']->diffForHumans() }}
                        </p>

                    </div>

                @empty

                    <div class="px-4 py-8 text-center text-slate-400">
                        No recent activity.
                    </div>

                @endforelse

            </div>

        </div>

        <!-- Avatar Only -->
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-sm font-semibold text-white sm:h-10 sm:w-10 sm:text-base">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>

    </div>

</header>
