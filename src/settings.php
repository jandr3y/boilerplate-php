<?php
return [
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false, 
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
            'user' => 'root',
            'pass' => '',
            'dbname' => 'slim'
        ],
        'jwtSecret' => 'zebra',
        'acl' => [
            'public' => [
                "Users" => [ "post" ]
            ],
            'user' => [
                "Users" => [ "get", "list", "delete" ]
            ],
            'admin' => [
                "Users" => [ "clear" ]
            ]
        ]
    ],
];


