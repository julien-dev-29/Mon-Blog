<?php

require 'public/index.php';

$migrations = [];
$seeds = [];

foreach ($modules as $module) {
    if ($module::MIGRATIONS !== null) {
        $migrations[] = $module::MIGRATIONS;
    }
    if ($module::SEEDS !== null) {
        $seeds[] = $module::SEEDS;
    }
}

return
    [
        'paths' => [
            'migrations' => $migrations,
            'seeds' => $seeds
        ],
        'environments' => [
            'development' => [
                'adapter' => 'mysql',
                'host' => $app->getContainer()->get('database.host'),
                'name' => $app->getContainer()->get('database.name'),
                'user' => $app->getContainer()->get('database.username'),
                'pass' => $app->getContainer()->get('database.password'),
                'port' => '3306',
                'charset' => 'utf8',
            ]
        ]
    ];
