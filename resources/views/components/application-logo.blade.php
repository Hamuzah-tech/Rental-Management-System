@php
    $logo = file_exists(public_path('images/alendi_logo.jpg'))
        ? asset('images/alendi_logo.jpg')
        : asset('images/default-logo.png');
@endphp

<img src="{{ $logo }}"
     alt="ALENDI"
     class="h-12 w-auto">