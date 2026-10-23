<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Every public URL is prefixed with one of these keys. The first locale is
    | the default one visitors land on when they open the bare domain.
    |
    */

    'locales' => [
        'ar' => ['name' => 'العربية', 'dir' => 'rtl'],
        'en' => ['name' => 'English', 'dir' => 'ltr'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead Notifications
    |--------------------------------------------------------------------------
    |
    | New service requests, Saudi Gate enquiries and job applications are sent
    | to this address. The "notification email" site setting takes priority.
    |
    */

    'notify_email' => env('LEADS_NOTIFY_EMAIL', 'info@advisory.com.sa'),

    /*
    |--------------------------------------------------------------------------
    | Brand Imagery
    |--------------------------------------------------------------------------
    |
    | Photographs from the brand identity (public/images/photos). They are the
    | defaults shown until a photo is uploaded in the admin for that place.
    |
    */

    'images' => [
        'hero' => 'images/photos/network.jpg',
        'intro' => 'images/photos/professionals.jpg',

        'pages' => [
            'about' => 'images/photos/arabian-peninsula.jpg',
            'services' => 'images/photos/network-wide.jpg',
            'saudi-gate' => 'images/photos/flags.jpg',
            'insights' => 'images/photos/idea.jpg',
            'impact' => 'images/photos/rosette.jpg',
            'careers' => 'images/photos/climber.jpg',
            'request-service' => 'images/photos/quality.jpg',
        ],

        'services' => [
            'strategy-growth' => 'images/photos/compass.jpg',
            'organizational-development' => 'images/photos/puzzle.jpg',
            'transformation-management' => 'images/photos/power.jpg',
            'business-process-outsourcing' => 'images/photos/tablet.jpg',
        ],
    ],

];
