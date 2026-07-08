<aside class="w-64 bg-slate-900 text-slate-200 flex flex-col min-h-screen">

    <div class="px-6 py-5 border-b border-slate-800">
        <h1 class="text-xl font-bold text-white">
            Rental MS
        </h1>

        <p class="text-sm text-slate-400">
            Super Administrator
        </p>
    </div>


    <nav class="flex-1 mt-4">


        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">

            <x-heroicon-o-home class="w-5 h-5"/>

            Dashboard

        </a>



        <a href="{{ route('admin.landlords.index') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.landlords.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">

            <x-heroicon-o-users class="w-5 h-5"/>

            Landlords

        </a>



        <a href="{{ route('admin.properties.index') }}"
           class="flex items-center gap-3 px-6 py-3 transition
           {{ request()->routeIs('admin.properties.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">

            <x-heroicon-o-building-office class="w-5 h-5"/>

            Properties

        </a>



                <a href="{{ route('admin.tenants.index') }}"
        class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition">

        <x-heroicon-o-user-group class="w-5 h-5"/>

        Tenants

        </a>



        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition">

            <x-heroicon-o-banknotes class="w-5 h-5"/>

            Payments

        </a>



        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition">

            <x-heroicon-o-chart-bar class="w-5 h-5"/>

            Reports

        </a>



        <a href="#"
           class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800 transition">

            <x-heroicon-o-cog-6-tooth class="w-5 h-5"/>

            Settings

        </a>


    </nav>



    <div class="p-4 border-t border-slate-800">

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                class="flex items-center justify-center gap-2 w-full bg-red-600 hover:bg-red-700 rounded-lg py-2">

                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5"/>

                Logout

            </button>

        </form>

    </div>


</aside>