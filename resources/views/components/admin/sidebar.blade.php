<!-- resources/views/components/admin/sidebar.blade.php -->
<aside class="w-64 bg-white border-r border-slate-200 text-slate-700 flex flex-col min-h-screen">

    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-200">
        <h1 class="text-xl font-bold text-slate-800">Rentals</h1>
        <p class="text-sm text-slate-500">Administrator</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 mt-4">
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
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Trash</p>
        </div>

        <!-- Trash Properties -->
        <a href="{{ route('admin.trash.properties') }}"
           class="flex items-center gap-3 px-6 py-3 transition pl-10
           {{ request()->routeIs('admin.trash.properties') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Properties</span>
            @php 
                $propertyCount = \App\Models\Property::onlyTrashed()->count(); 
            @endphp
            @if($propertyCount > 0)
                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $propertyCount }}</span>
            @endif
        </a>

        <!-- Trash Tenants -->
        <a href="{{ route('admin.trash.tenants') }}"
           class="flex items-center gap-3 px-6 py-3 transition pl-10
           {{ request()->routeIs('admin.trash.tenants') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Tenants</span>
            @php 
                $tenantCount = \App\Models\Tenant::onlyTrashed()->count(); 
            @endphp
            @if($tenantCount > 0)
                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $tenantCount }}</span>
            @endif
        </a>

        <!-- Trash Landlords -->
        <a href="{{ route('admin.trash.landlords') }}"
           class="flex items-center gap-3 px-6 py-3 transition pl-10
           {{ request()->routeIs('admin.trash.landlords') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Landlords</span>
            @php 
                $landlordCount = \App\Models\User::role('Landlord')->onlyTrashed()->count(); 
            @endphp
            @if($landlordCount > 0)
                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $landlordCount }}</span>
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
    <div class="p-4 border-t border-slate-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center justify-center gap-2 w-full border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl py-2 transition">
                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5"/>
                Logout
            </button>
        </form>
    </div>
</aside>