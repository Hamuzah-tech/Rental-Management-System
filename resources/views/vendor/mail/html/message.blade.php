<x-mail::layout>
    {{-- Greeting --}}
    @if (! empty($greeting))
        <div class="greeting">
            {{ $greeting }}
        </div>
    @else
        @php
            $isError = isset($level) && $level === 'error';
            $greetingText = $isError ? __('Whoops!') : __('Hello!');
        @endphp
        <div class="greeting">
            {{ $greetingText }}
        </div>
    @endif

    {{-- Intro Lines --}}
    @if (! empty($introLines))
        @foreach ($introLines as $line)
            <p>{{ $line }}</p>
        @endforeach
    @endif

    {{-- Action Button --}}
    @if (! empty($actionText))
        <x-mail::button :url="$actionUrl" :color="$color ?? 'primary'">
            {{ $actionText }}
        </x-mail::button>
    @endif

    {{-- Outro Lines --}}
    @if (! empty($outroLines))
        @foreach ($outroLines as $line)
            <p>{{ $line }}</p>
        @endforeach
    @endif

    {{-- Salutation --}}
    @if (! empty($salutation))
        <p>{{ $salutation }}</p>
    @else
        <p>Regards,<br>{{ config('app.name', 'ALENDI') }}</p>
    @endif

    {{-- Subcopy --}}
    @if (! empty($actionText))
        <x-slot:subcopy>
            @lang(
                "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
                'into your web browser:',
                [
                    'actionText' => $actionText,
                ]
            ) 
            <br>
            <span class="break-all">
                <a href="{{ $actionUrl }}" target="_blank" rel="noopener noreferrer">{{ $displayableActionUrl }}</a>
            </span>
        </x-slot:subcopy>
    @endif
</x-mail::layout>