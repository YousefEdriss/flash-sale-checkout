<?php
return [
    'name' => env('APP_NAME', 'FlashSale'),
    'env' => env('APP_ENV', 'local'),
    'key' => env('APP_KEY'),
    'debug' => env('APP_DEBUG', true),
    'providers' => [
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Log\LogServiceProvider::class,
    ],
];
