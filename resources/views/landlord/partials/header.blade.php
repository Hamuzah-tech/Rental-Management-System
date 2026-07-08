<header class="bg-white shadow px-6 py-4 flex justify-between items-center">


<h2 class="text-xl font-bold">

@yield('page-title')

</h2>



<div class="flex items-center gap-3">


<x-heroicon-o-user-circle class="w-8 h-8 text-gray-500"/>


<div>

<p class="font-semibold">

{{ auth()->user()->name }}

</p>


<p class="text-sm text-gray-500">

Landlord

</p>


</div>


</div>


</header>