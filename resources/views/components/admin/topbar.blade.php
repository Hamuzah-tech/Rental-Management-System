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

<header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">

    <!-- Page Title -->
    <div>
        <h1 class="text-2xl font-semibold text-slate-800">
            @yield('page-title', 'Dashboard')
        </h1>
    </div>

    <div class="flex items-center gap-6">

        <!-- Notifications -->
        <div class="relative">

            <button
                onclick="document.getElementById('notificationMenu').classList.toggle('hidden')"
                class="relative text-slate-500 hover:text-slate-700 transition">

                <x-heroicon-o-bell class="w-6 h-6"/>

                @if($notificationCount > 0)
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        {{ $notificationCount }}
                    </span>
                @endif

            </button>

            <!-- Notification Dropdown -->
            <div
                id="notificationMenu"
                class="hidden absolute right-0 mt-3 w-96 bg-white rounded-xl shadow-xl border border-slate-200 z-50">

                <div class="px-4 py-3 border-b">
                    <h3 class="font-semibold text-slate-700">
                        Recent Activity
                    </h3>
                </div>

                @forelse($notifications as $notification)

                    <div class="px-4 py-3 border-b hover:bg-slate-50">

                        <p class="text-sm text-slate-700">
                            {{ $notification['message'] }}
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
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
        <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white font-semibold">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>

    </div>

</header>