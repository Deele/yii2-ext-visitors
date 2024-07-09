<?php

$config = [
    'aliases' => [
        '@visitors' => '@app/extensions/visitors',
    ],
    'controllerMap' => [
        'migrate' => [
            'migrationNamespaces' => [
                'visitors\migrations',
            ],
        ],
    ],
];

return $config;
