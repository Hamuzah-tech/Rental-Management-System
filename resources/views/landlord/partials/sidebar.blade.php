<!-- resources/views/landlord/partials/sidebar.blade.php -->
<div x-data="{ open: window.innerWidth >= 1024 }" 
     x-init="
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                open = true;
            } else {
                open = false;
            }
        });
     ">

    <!-- Mobile Hamburger -->
    <button @click="open = !open" class="fixed top-4 left-4 z-50 lg:hidden p-2 rounded-lg hover:bg-gray-100 transition">
        <div class="space-y-1.5">
            <span class="block w-8 h-0.5 bg-gray-700 rounded-full"></span>
            <span class="block w-5 h-0.5 bg-gray-700 rounded-full"></span>
            <span class="block w-6 h-0.5 bg-gray-700 rounded-full"></span>
        </div>
    </button>

    <!-- Overlay - Only for mobile -->
    <div x-show="open && window.innerWidth < 1024" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         @click="open = false" 
         class="fixed inset-0 bg-black/40 z-40 lg:hidden"
         x-cloak>
    </div>

    <!-- Sidebar - Sticky, Never Disappears on Desktop -->
    <aside x-show="open" 
           x-transition:enter.duration.300ms
           x-transition:leave.duration.300ms
           class="fixed top-0 left-0 z-50 w-[270px] h-screen bg-white border-r border-slate-200 flex flex-col overflow-hidden"
           :class="{
               'lg:!translate-x-0': true,
               'translate-x-0': open && window.innerWidth >= 1024,
               '-translate-x-full': !open && window.innerWidth < 1024
           }"
           style="display: none;"
           x-cloak>

        <!-- Logo - Fixed at top -->
        <div class="flex-shrink-0 px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-white">
            <div>
                <h1 class="text-lg font-bold text-slate-800">Alendi</h1>
                <p class="text-xs text-slate-500">For Landlords. For Tenants.</p>
            </div>
            <button @click="open = false" class="lg:hidden text-slate-500 hover:text-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation - Scrollable middle section -->
        <nav class="flex-1 overflow-y-auto py-3 space-y-0.5">
            <!-- Dashboard -->
            <a href="{{ route('landlord.dashboard') }}"
               class="flex items-center gap-3 mx-2 px-4 py-2.5 rounded-lg transition-all duration-200 text-sm
               {{ request()->routeIs('landlord.dashboard') ? 'bg-[#5C8BD9] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <x-heroicon-o-home class="w-5 h-5 {{ request()->routeIs('landlord.dashboard') ? 'text-white' : 'text-slate-400' }}" />
                Dashboard
            </a>

            <!-- Properties (Hostels) -->
            <a href="{{ route('landlord.properties.index') }}"
               class="flex items-center gap-3 mx-2 px-4 py-2.5 rounded-lg transition-all duration-200 text-sm
               {{ request()->routeIs('landlord.properties.*') ? 'bg-[#5C8BD9] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <x-heroicon-o-building-office-2 class="w-5 h-5 {{ request()->routeIs('landlord.properties.*') ? 'text-white' : 'text-slate-400' }}" />
                Hostels
            </a>

            <!-- Tenants -->
            <a href="{{ route('landlord.tenants.index') }}"
               class="flex items-center gap-3 mx-2 px-4 py-2.5 rounded-lg transition-all duration-200 text-sm
               {{ request()->routeIs('landlord.tenants.*') ? 'bg-[#5C8BD9] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <x-heroicon-o-users class="w-5 h-5 {{ request()->routeIs('landlord.tenants.*') ? 'text-white' : 'text-slate-400' }}" />
                Tenants
            </a>

            <!-- Payments -->
            <a href="{{ route('landlord.payments.index') }}"
               class="flex items-center gap-3 mx-2 px-4 py-2.5 rounded-lg transition-all duration-200 text-sm
               {{ request()->routeIs('landlord.payments.*') ? 'bg-[#5C8BD9] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <x-heroicon-o-banknotes class="w-5 h-5 {{ request()->routeIs('landlord.payments.*') ? 'text-white' : 'text-slate-400' }}" />
                Payments
            </a>

            <!-- Divider -->
            <div class="border-t border-slate-200 my-3 mx-4"></div>

            <!-- Archived Section -->
            <div class="px-4 py-1">
                <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Archive</p>
            </div>

            <!-- Archived Hostels -->
            <a href="{{ route('landlord.properties.trashed') }}"
               class="flex items-center gap-3 mx-2 px-4 py-2.5 rounded-lg transition-all duration-200 text-sm
               {{ request()->routeIs('landlord.properties.trashed') ? 'bg-[#5C8BD9] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('landlord.properties.trashed') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hostels
                @php 
                    $propertyCount = \App\Models\Property::where('landlord_id', auth()->id())->onlyTrashed()->count(); 
                @endphp
                @if($propertyCount > 0)
                    <span class="ml-auto bg-amber-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $propertyCount }}</span>
                @endif
            </a>

            <!-- Archived Tenants -->
            <a href="{{ route('landlord.tenants.trashed') }}"
               class="flex items-center gap-3 mx-2 px-4 py-2.5 rounded-lg transition-all duration-200 text-sm
               {{ request()->routeIs('landlord.tenants.trashed') ? 'bg-[#5C8BD9] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('landlord.tenants.trashed') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Tenants
                @php 
                    $tenantCount = \App\Models\Tenant::whereHas('property', function($q) {
                        $q->where('landlord_id', auth()->id());
                    })->onlyTrashed()->count(); 
                @endphp
                @if($tenantCount > 0)
                    <span class="ml-auto bg-amber-500 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $tenantCount }}</span>
                @endif
            </a>
        </nav>

        <!-- Logout - Fixed at bottom -->
        <div class="flex-shrink-0 p-4 border-t border-slate-200 bg-white">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center justify-center gap-3 px-4 py-2.5 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200 text-sm">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                    Logout
                </button>
            </form>
        </div>

    </aside>
</div>