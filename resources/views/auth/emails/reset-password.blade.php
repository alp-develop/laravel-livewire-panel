@php
    $primaryColor = '#4f46e5';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('panel::messages.reset_password_title') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f7;padding:40px 0">
        <tr>
            <td align="center">
                <table role="presentation" width="570" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 4px rgba(0,0,0,0.05)">
                    <tr>
                        <td style="padding:32px 40px 24px;text-align:center;background-color:{{ $primaryColor }}">
                            <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:600">{{ __('panel::messages.reset_password_title') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 40px">
                            <p style="margin:0 0 16px;color:#51545e;font-size:15px;line-height:1.6">
                                {{ __('panel::messages.reset_password_email_line1') }}
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" style="display:inline-block;padding:14px 36px;background-color:{{ $primaryColor }};color:#ffffff;font-size:15px;font-weight:600;text-decoration:none;border-radius:6px">
                                            {{ __('panel::messages.reset_password_action') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 16px;color:#51545e;font-size:15px;line-height:1.6">
                                {{ __('panel::messages.reset_password_email_line2', ['count' => $expireMinutes]) }}
                            </p>
                            <p style="margin:0;color:#51545e;font-size:15px;line-height:1.6">
                                {{ __('panel::messages.reset_password_email_line3') }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 40px;border-top:1px solid #eaeaec">
                            <p style="margin:0;color:#a8aaaf;font-size:12px;line-height:1.5;text-align:center">
                                {{ config('app.name', 'Panel') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
