<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
<head>
    <title>{{ _lang('Oriental Wallet Email') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom-style.css') }}" rel="stylesheet" />
</head>

<!-- Email template container-->
<table border="0" cellpadding="0" height="100" width="100%">
    <tr>
        <td align="center" valign="top" class="email-container">
            <!-- Email content -->
            <table border="0" cellpadding="0" cellspacing="0" width="600    ">
            <tr>
                <td align="center">
                    <img src="{{ asset('images/logos/oriental-logo-white.jpg') }}" style="width:40%">
                </td>
            </tr>
            <tr>
                <td>
                    Dear <i>Mr/Ms {{ ucfirst($user->first_name) . " " . ucfirst($user->last_name) }}</i>,
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        {{ _lang('Thank you for using Oriental Wallet.') }}
                    </p>
                    <p>
                        {{ _lang('Please click the link below to complete the registration process, the link is valid for 24 hours.') }}
                    </p>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <a href="{{ $user->verification_url }}" class="btn btn-email">
                        {{ _lang('Verify Email') }}
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        {{ _lang('If you have any questions or concerns, please contact us anytime through our customer support e-mail.') }}
                    </p>
                    <p>
                        {{ _lang('Thank you for your continued patronage. We are committed to providing our customers with high-quality service.
') }}
                    </p> <br />
                </td>
            </tr>
            <!-- Footer -->
            <tr>
                <td>
                    <hr style="color:gray" />
                    <h4>
                        {{ _lang('Oriental Wallet Customer Support') }}
                    </h4>
                    <a href="https://www.orientawallet.com">https://www.orientawallet.com</a><br />
                    <a href={{"mailto:".env('MAIL_CONTACT')}}>{{ env('MAIL_CONTACT') }}</a><br />
                    <hr style="color:gray" />
                </td>
            </tr>
        </table>
        </td>
    </tr>
</table>
</body>
</html>

