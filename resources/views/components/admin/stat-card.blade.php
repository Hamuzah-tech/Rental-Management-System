@props([
    'title',
    'value',
    'icon'
])


<div class="bg-white rounded-xl border border-slate-200 p-6">

    <div class="flex justify-between items-center">

        <div>

            <p class="text-sm text-slate-500">
                {{ $title }}
            </p>

            <h2 class="text-3xl font-bold mt-2 text-slate-800">
                {{ $value }}
            </h2>

        </div>


        <div class="p-3 rounded-xl bg-slate-100 text-slate-300">

            {{ $icon }}

        </div>


    </div>

</div>