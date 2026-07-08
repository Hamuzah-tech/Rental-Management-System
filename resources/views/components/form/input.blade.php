@props([
    'label',
    'name',
    'type' => 'text'
])

<div>

    <label class="block font-medium mb-2">
        {{ $label }}
    </label>

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name) }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
        ]) }}
    >

    @error($name)
        <p class="text-red-500 text-sm mt-1">
            {{ $message }}
        </p>
    @enderror

</div>