<div x-data="{ open: false }" x-init="$watch('open', value => {
    if (value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
})">

    <!-- Mobile Hamburger -->
    <button
        @click="open = !open"
        class="fixed top-4 left-4 z-50 lg:hidden p-2 rounded-lg hover:bg-gray-100 transition">

        <div class="space-y-1.5">
            <span class="block w-8 h-0.5 bg-gray-700 rounded-full"></span>
            <span class="block w-5 h-0.5 bg-gray-700 rounded-full"></span>
            <span class="block w-6 h-0.5 bg-gray-700 rounded-full"></span>
        </div>

    </button>

    <!-- Overlay -->
    <div
        x-show="open"
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

    <!-- Sidebar -->
    <aside
        x-show="open"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        @click.away="open = false"
        class="fixed top-0 left-0 z-50
               w-64
               min-h-screen
               bg-white
               border-r border-slate-200
               flex
               flex-col
               lg:!translate-x-0 lg:!relative lg:!block"
        :class="{
            'hidden': !open,
            'lg:flex': true
        }"
        x-cloak>

        <!-- Logo -->
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">

            <div>
                <h1 class="text-xl font-bold text-slate-800">
                    Rentals
                </h1>
                <p class="text-sm text-slate-500">
                    Landlord
                </p>
            </div>

            <button
                @click="open = false"
                class="lg:hidden text-slate-500 hover:text-slate-800">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>

                </svg>

            </button>

        </div>

        <!-- Navigation -->
        <nav class="flex-1 mt-4 space-y-1">

            <!-- Dashboard -->
            <a href="{{ route('landlord.dashboard') }}"
               class="flex items-center gap-3 mx-2 px-4 py-3 transition-all duration-200
               {{ request()->routeIs('landlord.dashboard')
                    ? 'bg-[#5C8BD9] text-white'
                    : 'text-slate-700 hover:bg-gray-100 hover:text-slate-800'
               }}">

                <x-heroicon-o-home
                    class="w-5 h-5 {{ request()->routeIs('landlord.dashboard') ? 'text-white' : 'text-[#C0C9D6]' }}" />

                Dashboard

            </a>

            <!-- Properties -->
            <a href="{{ route('landlord.properties.index') }}"
               class="flex items-center gap-3 mx-2 px-4 py-3 transition-all duration-200
               {{ request()->routeIs('landlord.properties.*')
                    ? 'bg-[#5C8BD9] text-white'
                    : 'text-slate-700 hover:bg-gray-100 hover:text-slate-800'
               }}">

                <x-heroicon-o-building-office-2
                    class="w-5 h-5 {{ request()->routeIs('landlord.properties.*') ? 'text-white' : 'text-[#C0C9D6]' }}" />

                Properties

            </a>

            <!-- Tenants -->
            <a href="{{ route('landlord.tenants.index') }}"
               class="flex items-center gap-3 mx-2 px-4 py-3 transition-all duration-200
               {{ request()->routeIs('landlord.tenants.*')
                    ? 'bg-[#5C8BD9] text-white'
                    : 'text-slate-700 hover:bg-gray-100 hover:text-slate-800'
               }}">

                <x-heroicon-o-users
                    class="w-5 h-5 {{ request()->routeIs('landlord.tenants.*') ? 'text-white' : 'text-[#C0C9D6]' }}" />

                Tenants

            </a>
            
            <!-- Payments -->
            <a href="{{ route('landlord.payments.index') }}"
               class="flex items-center gap-3 mx-2 px-4 py-3 transition-all duration-200
               {{ request()->routeIs('landlord.payments.*')
                    ? 'bg-[#5C8BD9] text-white'
                    : 'text-slate-700 hover:bg-gray-100 hover:text-slate-800'
               }}">

                <x-heroicon-o-banknotes
                    class="w-5 h-5 {{ request()->routeIs('landlord.payments.*') ? 'text-white' : 'text-[#C0C9D6]' }}" />

                Payments

            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    class="w-full flex items-center gap-3 mx-2 px-4 py-3 transition-all duration-200 text-red-600 hover:bg-red-50 hover:text-red-700">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                    Logout
                </button>
            </form>

        </nav>

    </aside>

</div>