<?php

declare(strict_types=1);

use yii\db\Connection;

$projectRoot = dirname(__DIR__);

return [
    'id' => 'yii2-db',
    'basePath' => $projectRoot,
    'vendorPath' => $projectRoot,
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host=' . TEST_DB_HOST . ';dbname=yii2_db_tests',
            'username' => TEST_DB_USERNAME,
            'password' => TEST_DB_PASSWORD,
        ],
    ],
];
