<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // database
        'db' => [
            'driver' => 'mysql',
            'host' => '10.0.0.227',
            'database' => 'teachings_api',
            'username' => 'teachings_api',
            'password' => 'X89rFGCgnxUXelgs',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_520_ci',
            'prefix'    => '',
        ]
    ],
];
