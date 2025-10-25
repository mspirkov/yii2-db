<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests;

use PHPUnit\Framework\TestCase;
use yii\console\Application;

abstract class AbstractTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var array<string, mixed> */
        $config = require __DIR__ . '/config.php';

        new Application($config);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        restore_error_handler();
        restore_exception_handler();
    }
}
