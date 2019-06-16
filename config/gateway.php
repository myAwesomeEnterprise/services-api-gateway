<?php

return [
    /**
     * Guzzle request options
     * see documentation in http://docs.guzzlephp.org/en/stable/request-options.html
     * Note: only the keys in http_client are part of guzzle configurations
     */
    'http_client' => [
        'debug' => env('GATEWAY_HTTP_CLIENT_DEBUG', false),
        'timeout' => env('GATEWAY_HTTP_CLIENT_TIMEOUT', 3.0),
        'connect_timeout' => env('GATEWAY_HTTP_CLIENT_CONNECT_TIMEOUT', 2.0),
        'http_errors' => false,
        'allow_redirects' => [
            'max'             => env('GATEWAY_HTTP_CLIENT_MAX_REDIRECTS', 5),
            'strict'          => false,
            'referer'         => false,
            'protocols'       => ['http', 'https'],
            'track_redirects' => false
        ],
    ],

    /**
     * Available services
     */
    'services' => [
        'users' => [
            'host' => env('GATEWAY_USERS_SERVICE_HOST', 'https://jsonplaceholder.typicode.com'),
            'middleware' => [],
        ],
        'posts' => [
            'host' => env('GATEWAY_POSTS_SERVICE_HOST', 'https://jsonplaceholder.typicode.com'),
            'middleware' => [],
        ]
    ],
];
