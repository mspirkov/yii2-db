<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);

require "{$projectRoot}/vendor/autoload.php";
require "{$projectRoot}/vendor/yiisoft/yii2/Yii.php";
require __DIR__ . '/constants.php';

$pdo = new PDO('mysql:host=' . TEST_DB_HOST, TEST_DB_USERNAME, TEST_DB_PASSWORD, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

/** @var string */
$dbSql = file_get_contents(__DIR__ . '/db.sql');
$pdo->exec($dbSql);
