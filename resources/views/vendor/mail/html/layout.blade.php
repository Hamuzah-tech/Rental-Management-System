<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light dark" />
    <meta name="supported-color-schemes" content="light dark" />
    <title>{{ config('app.name', 'ALENDI') }}</title>
    <style>
        @import url('{{ asset('vendor/mail/html/themes/default.css') }}');
    </style>
</head>
<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td class="email-container" align="center">
                            <table class="email-body" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td>
                                        <!-- Header -->
                                        @include('mail::header')
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <!-- Body -->
                                        {{ $slot }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <!-- Subcopy -->
                                        @isset($subcopy)
                                            @include('mail::subcopy')
                                        @endisset
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <!-- Footer -->
                                        @include('mail::footer')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>