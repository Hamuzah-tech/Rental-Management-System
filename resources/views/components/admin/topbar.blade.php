<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">

    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            @yield('page-title', 'Dashboard')
        </h1>
    </div>

    <div class="flex items-center gap-6">

        <!-- Notifications -->
        <button class="relative text-gray-600 hover:text-indigo-600 transition">

            <x-heroicon-o-bell class="w-6 h-6"/>

            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                0
            </span>

        </button>

        <!-- User -->
        <div class="flex items-center gap-3">

            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>

            <div>

                <div class="font-semibold">
                    {{ auth()->user()->name }}
                </div>

                <div class="text-sm text-gray-500">
                    {{ auth()->user()->getRoleNames()->first() }}
                </div>

            </div>

        </div>

    </div>

</header>