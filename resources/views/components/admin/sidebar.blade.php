<!-- resources/views/components/admin/sidebar.blade.php -->
<aside
    class="fixed inset-y-0 left-0 z-50 flex h-full min-h-screen w-64 flex-col border-r border-slate-200 bg-white text-slate-700 shadow-xl transition-transform duration-300 lg:static lg:z-auto lg:translate-x-0 lg:shadow-none"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>

    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Rentals</h1>
            <p class="text-sm text-slate-500">Administrator</p>
        </div>
        <button
            type="button"
            @click="sidebarOpen = false"
            class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-800 lg:hidden"
            aria-label="Close menu"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="mt-4 flex-1 overflow-y-auto">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.dashboard') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <x-heroicon-o-home class="w-5 h-5 text-slate-400"/>
            <span>Dashboard</span>
        </a>

        <!-- Landlords -->
        <a href="{{ route('admin.landlords.index') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.landlords.index') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <x-heroicon-o-users class="w-5 h-5 text-slate-400"/>
            <span>Landlords</span>
        </a>

        <!-- Properties -->
        <a href="{{ route('admin.properties.index') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.properties.index') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <x-heroicon-o-building-office class="w-5 h-5 text-slate-400"/>
            <span>Properties</span>
        </a>

        <!-- Tenants -->
        <a href="{{ route('admin.tenants.index') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.tenants.index') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <x-heroicon-o-user-group class="w-5 h-5 text-slate-400"/>
            <span>Tenants</span>
        </a>

        <!-- Trash Section -->
        <div class="mt-4 px-4 py-2">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Trash</p>
        </div>

        <!-- Trash Properties -->
        <a href="{{ route('admin.trash.properties') }}"
           class="flex items-center gap-3 px-6 py-3 pl-10 transition
           {{ request()->routeIs('admin.trash.properties') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Properties</span>
            @php
                $propertyCount = \App\Models\Property::onlyTrashed()->count();
            @endphp
            @if($propertyCount > 0)
                <span class="ml-auto rounded-full bg-red-500 px-2 py-1 text-xs text-white">{{ $propertyCount }}</span>
            @endif
        </a>

        <!-- Trash Tenants -->
        <a href="{{ route('admin.trash.tenants') }}"
           class="flex items-center gap-3 px-6 py-3 pl-10 transition
           {{ request()->routeIs('admin.trash.tenants') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Tenants</span>
            @php
                $tenantCount = \App\Models\Tenant::onlyTrashed()->count();
            @endphp
            @if($tenantCount > 0)
                <span class="ml-auto rounded-full bg-red-500 px-2 py-1 text-xs text-white">{{ $tenantCount }}</span>
            @endif
        </a>

        <!-- Trash Landlords -->
        <a href="{{ route('admin.trash.landlords') }}"
           class="flex items-center gap-3 px-6 py-3 pl-10 transition
           {{ request()->routeIs('admin.trash.landlords') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Landlords</span>
            @php
                $landlordCount = \App\Models\User::where('role', 'landlord')
                    ->onlyTrashed()
                        ->count();
            @endphp
            @if($landlordCount > 0)
                <span class="ml-auto rounded-full bg-red-500 px-2 py-1 text-xs text-white">{{ $landlordCount }}</span>
            @endif
        </a>

        <!-- Settings -->
        <div class="mt-4 border-t border-slate-200 pt-4">
            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3 px-6 py-3 transition
               {{ request()->routeIs('admin.settings.*') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
                <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-slate-400"/>
                <span>Settings</span>
            </a>
        </div>
    </nav>

    <!-- Logout -->
    <div class="border-t border-slate-200 p-4">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 py-2 text-slate-600 transition hover:bg-slate-50">
                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5"/>
                Logout
            </button>
        </form>
    </div>
</aside>
