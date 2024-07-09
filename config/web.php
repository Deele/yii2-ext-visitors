<?php

$config = [
    'bootstrap' => [
        'visitors' => 'visitors',
    ],
    'aliases' => [
        '@visitors' => '@app/extensions/visitors',
    ],
    'components' => [
        'i18n' => [
            'translations' => [
                'visitors.*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@visitors/messages',
                ],
            ],
        ],
    ],
    'modules' => [
        'visitors' => 'visitors\VisitorsModule',
    ],
];

return $config;
