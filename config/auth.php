<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Default guard = stafaset (karena proyek kamu pakai tabel stafaset).
    | Bisa diganti ke 'web' kalau masih mau pakai users bawaan Laravel.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'stafaset'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'stafasets'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Guard = cara Laravel autentikasi user.
    | Kita pakai 2 guard: "web" (default Laravel) dan "stafaset" (custom).
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'stafaset' => [
            'driver' => 'session',
            'provider' => 'stafasets',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Provider menentukan dari mana data user diambil.
    | stafasets diambil dari model App\Models\Stafaset.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        'stafasets' => [
            'driver' => 'eloquent',
            'model' => App\Models\StafAset::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Konfigurasi password reset untuk kedua jenis user.
    | stafasets akan punya tabel token reset sendiri.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'stafasets' => [
            'provider' => 'stafasets',
            'table' => 'stafaset_password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Waktu dalam detik sebelum sesi konfirmasi password kadaluarsa.
    | Default: 3 jam.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
