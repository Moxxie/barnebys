<?php
$config = [
    'production' => [
        'determineRouteBeforeAppMiddleware' => true,
        'db' => [
            'database_type' => 'mysql',
            'database_name' => '',
            'server' => '',
            'username' => '',
            'password' => '',
            'charset' => 'utf8'
        ],
        'displayErrorDetails' => false,
        'baseUri' => '/',
        'cache' => [
          'ttl' => 120
        ]
    ],
    'development' => [
        'determineRouteBeforeAppMiddleware' => true,
        'db' => [
            'database_type' => 'mysql',
            'database_name' => 'barnebys',
            'server' => 'localhost',
            'username' => 'root',
            'password' => 'nahtanoj',
            'charset' => 'utf8',
        ],
        'displayErrorDetails' => true,
        'baseUri' => '/barnebys/api/'
    ]
];