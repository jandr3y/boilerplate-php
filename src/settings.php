<?php
return [
    'settings' => [
        'dev' => true,
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false, 
        'determineRouteBeforeAppMiddleware' => true,
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'db' => [
            'host' => 'localhost',
            'user' => 'nedemo',
            'pass' => 'nedemo',
            'dbname' => 'saturno'
        ],
        'jwtSecret' => 'zebra',
        'acl' => [
            'public' => [
                "post" => [ 
                    "/users", 
                    "/auth",
                ],
                "get" => [
                    "/admin"
                ]
            ],
            'user' => [
                "get" => [ 
                    "/users",
                    "/users/{username}"
                ],
                "delete" => [
                    "/users/{id}"
                ],
                'put' => [
                    "/users/{id}"
                ]
            ],
            'admin' => [
                "get" => [ 
                    "/admin/home",
                    "/admin/{model}"
                ]
            ]
        ]
    ],
];


