<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db;

use Throwable;
use Yii;
use yii\db\Connection;

/**
 * A utility class for managing database transactions with a consistent and safe approach.
 *
 * This class simplifies the process of wrapping database operations within transactions,
 * ensuring that changes are either fully committed or completely rolled back in case of errors.
 *
 * It provides two main methods:
 *
 * - {@see TransactionManagerInterface::safeWrap()} - executes a callable within a transaction, safely handling
 *   exceptions and logging them.
 * - {@see TransactionManagerInterface::wrap()} - executes a callable within a transaction.
 *
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 *
 * @immutable
 */
final class TransactionManager implements TransactionManagerInterface
{
    /**
     * The current connection where transactions will be executed.
     */
    private Connection $connection;

    /**
     * @param Connection $connection The current connection where transactions will be executed.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function safeWrap(
        callable $function,
        ?string $isolationLevel = null,
        ?callable $logFunction = null
    ) {
        try {
            return $this->wrap($function, $isolationLevel);
        } catch (Throwable $e) {
            if ($logFunction !== null) {
                $logFunction($e);
            } else {
                Yii::error($e);
            }

            return false;
        }
    }

    public function wrap(callable $function, ?string $isolationLevel = null)
    {
        $transaction = $this->connection->beginTransaction($isolationLevel);

        try {
            $result = $function();
            $transaction->commit();

            return $result;
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }
    }
}
