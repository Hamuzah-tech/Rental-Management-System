@props([
    'title',
    'value',
    'icon',
    'color' => 'indigo'
])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

    <div class="flex justify-between items-center">

        <div>

            <p class="text-sm text-gray-500">
                {{ $title }}
            </p>

            <h2 class="text-3xl font-bold mt-2">
                {{ $value }}
            </h2>

        </div>

        <div class="p-3 rounded-xl bg-{{ $color }}-100">

            {{ $icon }}

        </div>

    </div>

</div>