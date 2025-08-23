<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Este array define el guard de autenticación por defecto y el broker de
    | reseteo de contraseñas. Puedes cambiar estos valores según lo requieras.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Aquí defines todos los guards de autenticación para tu aplicación.
    | El guard por defecto "web" usa sesión, pero también añadimos el de
    | repartidores, que usará el provider "riders".
    |
    | Soportados: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Nuevo guard para repartidores
        'repartidor' => [
            'driver' => 'session',
            'provider' => 'riders',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Los providers definen cómo se obtienen los usuarios de tu base de datos.
    | Puedes tener múltiples providers si tienes más de un tipo de usuario.
    |
    | Soportados: "eloquent", "database"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        // Nuevo provider para repartidores
        'riders' => [
            'driver' => 'eloquent',
            'model' => App\Models\Rider::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Aquí configuras cómo funciona el reseteo de contraseñas.
    | Cada broker de contraseña se puede asociar a un provider.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        // Opcional: si también quieres reset de contraseñas para repartidores
        'riders' => [
            'provider' => 'riders',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Tiempo en segundos para la expiración de confirmación de contraseña.
    | Por defecto son 3 horas.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
