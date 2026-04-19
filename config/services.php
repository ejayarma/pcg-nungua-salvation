<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'deywuro' => [
        'api_username' => env('DEYWURO_USERNAME'),
        'api_password' => env('DEYWURO_PASSWORD'),
        'sender_id' => env('DEYWURO_SENDER_ID'),
        'sms_url' => env('DEYWURO_API_URL', 'https://deywuro.com/api/sms'),
        'balance_url' => env('DEYWURO_CREDIT_URL', 'https://api.deywuro.com/bulksms/credit_bal.php'),
        'topup_url' => env('DEYWURO_TOPUP_URL', 'https://www.deywuro.com/api/make_payment'),
        'topup_password' => env('DEYWURO_TOPUP_PASSWORD', 'hdgt2314'),
        'topup_username' => env('DEYWURO_TOPUP_USERNAME', 'npdeywuro'),
        'topup_voucher_number' => env('DEYWURO_TOPUP_VOUCHER_NUMBER', '1'),
        'topup_uid' => env('DEYWURO_TOPUP_UID', '14058'),
        'topup_description' => env('DEYWURO_TOPUP_DESCRIPTION', 'Deywuro'),
    ],

];
