<?php

return [

    'title' => 'Install the website',
    'intro' => 'This page sets the website up on the hosting once: the database, the launch content and the first admin account.',
    'requirements' => 'Hosting check',
    'not_ready' => 'Fix the red items in the hosting settings (cPanel) or in the .env file, then refresh the page.',
    'account' => 'First admin account',
    'account_hint' => 'This is your dashboard login. You can add other users from inside the dashboard later.',
    'name' => 'Name',
    'email' => 'Email',
    'password' => 'Password',
    'password_hint' => 'At least 12 characters, with letters and numbers.',
    'password_confirmation' => 'Confirm password',
    'submit' => 'Install the website',
    'result' => 'Installation result',
    'done' => 'The website is installed.',
    'failed' => 'The installation did not finish. Fix the red step, then open the install link again.',
    'remove_token' => 'For extra safety, remove the INSTALL_TOKEN line from the .env file now.',
    'open_dashboard' => 'Open the dashboard',
    'open_site' => 'Open the website',
    'checks' => [
        'php' => 'PHP version (8.3 or newer)',
        'extension' => 'PHP extension: :name',
        'missing' => 'Not enabled',
        'writable' => 'Write access to :path',
        'app_key' => 'Application key (APP_KEY)',
        'database' => 'Database connection',
    ],
    'steps' => [
        'migrate' => 'Create the database tables',
        'seed' => 'Add the launch content',
        'admin' => 'Create the admin account',
        'storage_link' => 'Link the public uploads folder',
    ],

];
