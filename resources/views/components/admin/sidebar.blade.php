<aside class="w-64 bg-white border-r border-slate-200 text-slate-700 flex flex-col min-h-screen">

    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-200">

        <h1 class="text-xl font-bold text-slate-800">
            Rental MS
        </h1>

        <p class="text-sm text-slate-500">
            Super Administrator
        </p>

    </div>


    <!-- Navigation -->
    <nav class="flex-1 mt-4">


        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.dashboard') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">

            <x-heroicon-o-home class="w-5 h-5 text-slate-400"/>

            <span>
                Dashboard
            </span>

        </a>



        <!-- Landlords -->
        <a href="{{ route('admin.landlords.index') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.landlords.*') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">

            <x-heroicon-o-users class="w-5 h-5 text-slate-400"/>

            <span>
                Landlords
            </span>

        </a>



        <!-- Properties -->
        <a href="{{ route('admin.properties.index') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.properties.*') ? 'bg-slate-100 text-slate-900' : 'hover:bg-slate-50' }}">

            <x-heroicon-o-building-office class="w-5 h-5 text-slate-400"/>

            <span>
                Properties
            </span>

        </a>



        <!-- Tenants -->
        <a href="{{ route('admin.tenants.index') }}"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50 transition">

            <x-heroicon-o-user-group class="w-5 h-5 text-slate-400"/>

            <span>
                Tenants
            </span>

        </a>



        <!-- Payments -->
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50 transition">

            <x-heroicon-o-banknotes class="w-5 h-5 text-slate-400"/>

            <span>
                Payments
            </span>

        </a>



        <!-- Reports -->
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50 transition">

            <x-heroicon-o-chart-bar class="w-5 h-5 text-slate-400"/>

            <span>
                Reports
            </span>

        </a>



        <!-- Settings -->
        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50 transition">

            <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-slate-400"/>

            <span>
                Settings
            </span>

        </a>


    </nav>



    <!-- Logout -->
    <div class="p-4 border-t border-slate-200">

        <form method="POST" action="{{ route('logout') }}">

            @csrf

            <button
                type="submit"
                class="flex items-center justify-center gap-2 w-full border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl py-2 transition">

                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5"/>

                Logout

            </button>

        </form>

    </div>


</aside>
