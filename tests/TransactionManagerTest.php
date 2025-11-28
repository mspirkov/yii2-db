<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests;

use Exception;
use Throwable;
use Yii;
use MSpirkov\Yii2\Db\TransactionManager;
use yii\log\Logger;

class TransactionManagerTest extends AbstractTestCase
{
    private TransactionManager $transactionManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionManager = new TransactionManager(Yii::$app->db);
    }

    public function testWrap(): void
    {
        $customerName = 'Test1';

        /** @var bool */
        $transactionResult = $this->transactionManager->wrap(function () use ($customerName): bool {
            $this->executeCommand(
                "INSERT INTO `customers` (email, name) VALUES ('testwrap1@gmail.com', '{$customerName}')"
            );
            $this->executeCommand(
                "INSERT INTO `customers` (email, name) VALUES ('testwrap2@gmail.com', '{$customerName}')"
            );

            return true;
        });

        self::assertTrue($transactionResult);

        $rowsCount = (int) Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM `customers` WHERE name = '{$customerName}'"
        )->queryScalar();

        self::assertSame(2, $rowsCount);

        $exception = new Exception('Test transactions 1');

        try {
            $this->transactionManager->wrap(function () use ($customerName, $exception): void {
                $this->executeCommand(
                    "INSERT INTO `customers` (email, name) VALUES ('testwrap3@gmail.com', '{$customerName}')"
                );
                $this->executeCommand(
                    "INSERT INTO `customers` (email, name) VALUES ('testwrap4@gmail.com', '{$customerName}')"
                );

                throw $exception;
            });
        } catch (Exception $transactionException) {
        }

        self::assertSame($exception, $transactionException);

        $rowsCount = (int) Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM `customers` WHERE name = '{$customerName}'"
        )->queryScalar();

        self::assertSame(2, $rowsCount);
    }

    public function testSafeWrap(): void
    {
        $customerName = 'Test2';

        /** @var bool */
        $transactionResult = $this->transactionManager->safeWrap(function () use ($customerName): bool {
            $this->executeCommand(
                "INSERT INTO `customers` (email, name) VALUES ('testsafewrap1@gmail.com', '{$customerName}')"
            );
            $this->executeCommand(
                "INSERT INTO `customers` (email, name) VALUES ('testsafewrap2@gmail.com', '{$customerName}')"
            );

            return true;
        });

        self::assertTrue($transactionResult);

        $rowsCount = (int) Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM `customers` WHERE name = '{$customerName}'"
        )->queryScalar();

        self::assertSame(2, $rowsCount);

        $exception = new Exception('Test transactions 2');

        /** @var bool */
        $transactionResult = $this->transactionManager->safeWrap(function () use ($customerName, $exception): void {
            $this->executeCommand(
                "INSERT INTO `customers` (email, name) VALUES ('testsafewrap3@gmail.com', '{$customerName}')"
            );
            $this->executeCommand(
                "INSERT INTO `customers` (email, name) VALUES ('testsafewrap4@gmail.com', '{$customerName}')"
            );

            throw $exception;
        });

        self::assertFalse($transactionResult);

        $loggedMessage = $this->getMessageFromLogger($exception->getMessage());
        self::assertNotNull($loggedMessage);
        self::assertSame($exception, $loggedMessage[0]);
        self::assertSame(Logger::LEVEL_ERROR, $loggedMessage[1]);

        $rowsCount = (int) Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM `customers` WHERE name = '{$customerName}'"
        )->queryScalar();

        self::assertSame(2, $rowsCount);
    }

    public function testSafeWrapWithLogFunction(): void
    {
        $customerName = 'Test3';

        $logFunction = function (Throwable $e): void {
            Yii::warning($e);
        };

        /** @var bool */
        $transactionResult = $this->transactionManager->safeWrap(
            function () use ($customerName): bool {
                $this->executeCommand(
                    "INSERT INTO `customers` (email, name) VALUES ('testsafewrap5@gmail.com', '{$customerName}')"
                );
                $this->executeCommand(
                    "INSERT INTO `customers` (email, name) VALUES ('testsafewrap6@gmail.com', '{$customerName}')"
                );

                return true;
            },
            null,
            $logFunction
        );

        self::assertTrue($transactionResult);

        $rowsCount = (int) Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM `customers` WHERE name = '{$customerName}'"
        )->queryScalar();

        self::assertSame(2, $rowsCount);

        $exception = new Exception('Test transactions 3');

        /** @var bool */
        $transactionResult = $this->transactionManager->safeWrap(
            function () use ($customerName, $exception): void {
                $this->executeCommand(
                    "INSERT INTO `customers` (email, name) VALUES ('testsafewrap7@gmail.com', '{$customerName}')"
                );
                $this->executeCommand(
                    "INSERT INTO `customers` (email, name) VALUES ('testsafewrap8@gmail.com', '{$customerName}')"
                );

                throw $exception;
            },
            null,
            $logFunction
        );

        self::assertFalse($transactionResult);

        $loggedMessage = $this->getMessageFromLogger($exception->getMessage());
        self::assertNotNull($loggedMessage);
        self::assertSame($exception, $loggedMessage[0]);
        self::assertSame(Logger::LEVEL_WARNING, $loggedMessage[1]);

        $rowsCount = (int) Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM `customers` WHERE name = '{$customerName}'"
        )->queryScalar();

        self::assertSame(2, $rowsCount);
    }

    private function executeCommand(string $sql): void
    {
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * @return array<int, mixed>|null
     */
    private function getMessageFromLogger(string $message): ?array
    {
        $result = null;

        /** @var array<int, mixed> $logMessage */
        foreach (Yii::$app->log->logger->messages as $logMessage) {
            $messageException = $logMessage[0];
            if ($messageException instanceof Exception && $messageException->getMessage() === $message) {
                $result = $logMessage;
                break;
            }
        }

        return $result;
    }
}
