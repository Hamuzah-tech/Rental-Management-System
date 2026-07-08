<aside class="w-64 bg-slate-900 text-slate-200 min-h-screen flex flex-col">


<div class="px-6 py-5 border-b border-slate-800">

<h1 class="text-xl font-bold text-white">

Rental MS

</h1>


<p class="text-sm text-slate-400">

Landlord Portal

</p>


</div>



<nav class="flex-1 mt-4">


    <a href="{{ route('landlord.dashboard') }}"
       class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800">

        <x-heroicon-o-home class="w-5 h-5"/>

        Dashboard

    </a>



    <a href="{{ route('landlord.properties.index') }}"
       class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800">

        <x-heroicon-o-building-office-2 class="w-5 h-5"/>

        Properties

    </a>



        <a href="{{ route('landlord.tenants.index') }}"
        class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800">

        <x-heroicon-o-users class="w-5 h-5"/>

        Tenants

        </a>


    <a href="#"
       class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800">

        <x-heroicon-o-banknotes class="w-5 h-5"/>

        Payments

    </a>



    <a href="#"
       class="flex items-center gap-3 px-6 py-3 hover:bg-slate-800">

        <x-heroicon-o-bell class="w-5 h-5"/>

        Move Out Notices

    </a>


</nav>



<div class="p-4 border-t border-slate-800">


<form method="POST" action="{{ route('logout') }}">

@csrf


<button
class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg flex items-center justify-center gap-2">


<x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5"/>


Logout


</button>


</form>


</div>


</aside>