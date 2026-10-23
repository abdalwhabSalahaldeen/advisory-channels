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

];
