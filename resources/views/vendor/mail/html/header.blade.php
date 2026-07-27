@php
    $logoPath = public_path('images/alendi_logo.jpg');
    $logoUrl = file_exists($logoPath) ? asset('images/alendi_logo.jpg') : 'https://via.placeholder.com/200x48?text=ALENDI';
@endphp

<table class="email-header" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <a href="{{ config('app.url', 'https://alendiestates.com') }}" target="_blank" rel="noopener noreferrer" style="text-decoration: none; display: inline-block;">
                <img src="{{ $logoUrl }}" alt="ALENDI" style="max-height: 48px; width: auto; display: block;" />
            </a>
        </td>
    </tr>
</table>