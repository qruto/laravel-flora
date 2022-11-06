<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Environment configuration key
    |--------------------------------------------------------------------------
    |
    | Config path, where current environment value stored
    */
    'env_config_key' => 'app.env',

    'assets' => [
        'production_build' => false,
        'published' => [
            'laravel-assets',
            \Laravel\Telescope\TelescopeServiceProvider::class,
        ],
    ]
];
