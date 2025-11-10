<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWSOWL_ACCESS_KEY_ID'),
        'secret' => env('AWSOWL_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'jp_voucher' => [
        'jp_integration_url' => env('JP_VOUCHER_INTEGRATION_URL','http://owl-jpay.com/uk'),
        'jp_integration_sid' => env('JP_VOUCHER_INTEGRATION_SID','619999222'),
    ],

    'rapid' => [
        'baseurl' => env('RAPID_BASE_URL','https://fixer-fixer-currency-v1.p.rapidapi.com/latest?base='),
        'host' => env('RAPID_HOST','fixer-fixer-currency-v1.p.rapidapi.com'),
        'key' => env('RAPID_KEY','e83a64ed76msh880cbbf802b5efep1675fajsnc368297c8022')
    ],

    'fixerio' => [
        'baseurl' => env('FIXERIO_BASE_URL'),
        'key' => env('FIXERIO_ACCESS_KEY')
    ],
];
