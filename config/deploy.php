<?php

/*
|--------------------------------------------------------------------------
| Deployment bootstrap
|--------------------------------------------------------------------------
|
| Hosts without shell access (Wasmer Edge deploying straight from GitHub, for example) can't
| run `php artisan migrate`, `db:seed` or `storage:link`. When AUTO_DEPLOY is on, the app
| runs those steps itself - see App\Support\Deployer. Everything it does is idempotent, so
| running it on every release (or twice) is harmless.
|
*/

return [

    // Run the bootstrap automatically on the first web request after each release.
    'auto' => (bool) env('AUTO_DEPLOY', false),

    // Optional shared secret for POST /__deploy (header "Authorization: Bearer <token>" or
    // "X-Deploy-Token"). Leave empty to allow the endpoint without a token - safe, because it
    // only ever performs the same idempotent steps as above.
    'token' => env('DEPLOY_TOKEN'),

    // Also load the demo students, videos, documents, blog posts, courses... (first run only).
    'demo' => (bool) env('DEPLOY_DEMO', false),

    // Minimum seconds between two runs of POST /__deploy (a simple guard against hammering it).
    'min_interval' => (int) env('DEPLOY_MIN_INTERVAL', 10),

    // Changing this forces the automatic bootstrap to run again for the same code.
    'release' => env('APP_RELEASE', ''),

    // The administrator account created on first run. Required outside local development.
    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin'),
        'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    // Password given to the demo student accounts (DEPLOY_DEMO). Change it on any public site.
    'demo_password' => env('DEMO_PASSWORD', 'password'),
];
