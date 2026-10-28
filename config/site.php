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

    /*
    |--------------------------------------------------------------------------
    | Dashboard Two-Factor Sign-In
    |--------------------------------------------------------------------------
    |
    | When on, signing in to the dashboard also needs an emailed one-time code
    | (or an authenticator app). Keep it off until MAIL_* is configured, or
    | nobody will receive their code.
    |
    */

    'admin_two_factor' => (bool) env('ADMIN_TWO_FACTOR', false),

    /*
    |--------------------------------------------------------------------------
    | File Manager
    |--------------------------------------------------------------------------
    |
    | The "Files" page of the dashboard browses these folders only. Uploads and
    | renames are limited to the extensions below, so nobody can place a PHP
    | script (or any other executable) in a folder the web server serves.
    |
    */

    'file_manager' => [
        'enabled' => env('FILE_MANAGER_ENABLED', true),

        'roots' => [
            'public' => storage_path('app/public'),
            'private' => storage_path('app/private'),
        ],

        // Folders the framework manages itself; they are never listed.
        'hidden' => ['livewire-tmp'],

        'max_upload_kb' => 20480,

        'max_edit_kb' => 1024,

        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'txt', 'md', 'json', 'xml',
            'zip', 'mp4', 'webm', 'mp3', 'woff', 'woff2',
        ],

        // Plain-text files that can be opened in the dashboard editor.
        'editable_extensions' => ['txt', 'md', 'csv', 'json', 'xml', 'svg'],

        'file_modes' => ['0644', '0664', '0640', '0600'],

        'directory_modes' => ['0755', '0775', '0750', '0700'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Web Installer
    |--------------------------------------------------------------------------
    |
    | Hosting without a terminal is set up from /install?token=... It is only
    | available while INSTALL_TOKEN is set and no admin account exists yet.
    |
    */

    'install_token' => env('INSTALL_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Deploy Hook
    |--------------------------------------------------------------------------
    |
    | After the GitHub workflow uploads a release over FTP it calls
    | POST /deploy/hook with this token to run the database updates and clear
    | the caches. The hook is disabled while DEPLOY_TOKEN is empty.
    |
    */

    'deploy_token' => env('DEPLOY_TOKEN'),

];
