<!-- resources/views/landlord/partials/header.blade.php -->

<header class="bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 flex-shrink-0">
    
    <!-- Page Title -->
    <h2 class="text-lg font-semibold text-slate-800">
        @yield('page-title', 'Dashboard')
    </h2>

    <!-- User Avatar with Dropdown - No Sidebar Interference -->
    <div x-data="{ open: false }" class="relative">
        <!-- Avatar Button -->
        <button @click="open = !open" 
                @click.away="open = false"
                class="flex items-center gap-2.5 focus:outline-none hover:opacity-80 transition">
            <div class="w-9 h-9 rounded-full bg-[#5C8BD9] flex items-center justify-center text-white font-medium text-sm">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        </button>

        <!-- Dropdown Menu -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="open = false"
             class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 z-50 overflow-hidden"
             style="display: none;">
            
            <!-- User Info -->
            <div class="px-4 py-3 border-b border-slate-100">
                <p class="font-medium text-slate-800 text-sm">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-slate-500">
                    Landlord
                </p>
            </div>
            
            <!-- Menu Items -->
            <div class="px-2 py-2">
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 text-sm text-slate-600 hover:text-slate-800 hover:bg-slate-50 px-3 py-2 rounded-lg transition-colors duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile Settings
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center gap-3 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded-lg transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

</header>