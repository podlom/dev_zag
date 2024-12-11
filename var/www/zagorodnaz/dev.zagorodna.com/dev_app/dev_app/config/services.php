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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google_places' => [
        'key' => 'AIzaSyCVaBnxRkpK93R_XLI9_jlV5nDg2ZC9gow'
    ],

    'telegram-bot-api' => [
        'token' => '1399376158:AAE1nlUBO3_tO4ZTzc6mcgBqFgviPBMKlj8'
    ],

    'mapbox' => [
        'token' => env('MAPBOX_API_TOKEN', 'pk.eyJ1Ijoic2h0YXJhcyIsImEiOiJjbTAxYTF2MGMxdTduMm1zYWR3aWNqZmpkIn0.fURlyO6_GrCV-EpWs0rYUw'),
    ],

];
