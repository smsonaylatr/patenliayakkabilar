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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
        'analytics_id' => env('GOOGLE_ANALYTICS_ID', 'G-ZMY22BQK1N'),
        'gtm_container_id' => env('GTM_CONTAINER_ID', 'GTM-583KKT3Q'),
        'tag_id' => env('GOOGLE_TAG_ID', ''),
        'ads_id' => env('GOOGLE_ADS_ID', ''),
    ],

    'paytr' => [
        'merchant_id' => env('PAYTR_MERCHANT_ID'),
        'merchant_key' => env('PAYTR_MERCHANT_KEY'),
        'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
    ],

    'etbis' => [
        'api_key' => env('ETBIS_API_KEY'),
        'site_id' => env('ETBIS_SITE_ID'),
    ],

    'google_places' => [
        'api_key' => env('GOOGLE_PLACES_API_KEY'),
    ],

    'porego' => [
        'api_key' => env('POREGO_API_KEY'),
        'api_secret' => env('POREGO_API_SECRET'),
        'api_url' => env('POREGO_API_URL', 'https://back.porego.com/depokargo/api/v1/merchant-api/v1'),
        'webhook_secret' => env('POREGO_WEBHOOK_SECRET'),
    ],

    'sonecentral' => [
        'api_url' => env('SONECENTRAL_API_URL', 'https://pb.sonecentral.com/api/v1'),
        'api_key' => env('SONECENTRAL_API_KEY', 'sk-sone-v1-veTfj10rXv5zcKWYtLrjXFsG0Y0QfPIvasmk1eB5SA6HcyRO'),
        'assistant_id' => env('SONECENTRAL_ASSISTANT_ID', '3nco7b3k5th1j2b'),
    ],

];
