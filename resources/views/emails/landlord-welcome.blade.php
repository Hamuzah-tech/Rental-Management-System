<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>

<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:40px 0;">
<tr>
<td align="center">

<table width="650" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.08);">

    <!-- Header -->
    <tr>
        <td align="center" style="background:#4F46E5;padding:35px;">

            <!-- Replace this later with your logo -->
            <h1 style="color:#ffffff;margin:0;">
                Rental Management System
            </h1>

            <p style="color:#E0E7FF;margin-top:10px;">
                Welcome to your landlord account
            </p>

        </td>
    </tr>

    <!-- Body -->
    <tr>
        <td style="padding:40px;">

            <h2 style="color:#111827;">
                Hello {{ $user->name }},
            </h2>

            <p style="font-size:15px;color:#4B5563;line-height:1.8;">
                Your landlord account has been created successfully.
                Below are your login credentials.
            </p>

            <table width="100%" cellpadding="12" cellspacing="0"
                   style="margin-top:25px;border:1px solid #E5E7EB;border-radius:8px;">

                <tr>
                    <td width="180"><strong>Username</strong></td>
                    <td>{{ $user->username }}</td>
                </tr>

                <tr>
                    <td><strong>Email</strong></td>
                    <td>{{ $user->email }}</td>
                </tr>

                <tr>
                    <td><strong>Temporary Password</strong></td>
                    <td>
                        <strong>{{ $password }}</strong>
                    </td>
                </tr>

            </table>

            <div style="margin:35px 0;text-align:center;">

                <a href="{{ url('/landlord/login') }}"
                   style="background:#4F46E5;color:#fff;text-decoration:none;padding:15px 35px;border-radius:8px;font-weight:bold;display:inline-block;">

                    Login to Your Account

                </a>

            </div>

            <div style="background:#FEF3C7;padding:20px;border-left:5px solid #F59E0B;border-radius:5px;">

                <strong>Security Notice</strong>

                <p style="margin-top:10px;color:#444;line-height:1.7;">
                    For your security, please log in immediately and change your password to something only you know.
                </p>

            </div>

        </td>
    </tr>

    <!-- Footer -->
    <tr>

        <td align="center"
            style="background:#F9FAFB;padding:25px;font-size:13px;color:#6B7280;">

            © {{ date('Y') }} Rental Management System<br>

            This email was automatically generated.
            Please do not reply to this email.

        </td>

    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>