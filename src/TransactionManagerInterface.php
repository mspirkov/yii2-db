<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db;

use Throwable;
use yii\db\Transaction;

/**
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 */
interface TransactionManagerInterface
{
    /**
     * Executes a callable within a transaction, safely handling exceptions and logging them.
     *
     * This method attempts to execute the provided callable within a database transaction. If an
     * exception occurs, the transaction is rolled back, and the exception is caught and logged.
     *
     * @template T
     *
     * @param callable(): T $function The callable to execute within the transaction.
     * @param string|null $isolationLevel The isolation level to use for the transaction. If `null`, the
     * default isolation level is used. See {@see Transaction::begin()} for possible values.
     * @param (callable(Throwable): void)|null $logFunction An optional callable to handle logging of
     * exceptions. If `null`, then `Yii::error` will be used to log the exception.
     *
     * @return T|false Returns the result of the callable if the transaction is successful, or `false`
     * if an exception occurs.
     */
    public function safeWrap(
        callable $function,
        ?string $isolationLevel = null,
        ?callable $logFunction = null
    );

    /**
     * Executes a callable within a transaction.
     *
     * This method attempts to execute the provided callable within a database transaction. If an
     * exception occurs, the transaction is rolled back and the exception is re-thrown.
     *
     * @template T
     *
     * @param callable(): T $function The callable to execute within the transaction.
     * @param string|null $isolationLevel The isolation level to use for the transaction. If `null`, the
     * default isolation level is used. See {@see Transaction::begin()} for possible values.
     *
     * @throws Throwable If an exception occurs within the callable or the transaction fails to start.
     *
     * @return T The result of the callable if the transaction is successful.
     */
    public function wrap(callable $function, ?string $isolationLevel = null);
}
