<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db;

use Throwable;
use Yii;
use yii\db\Connection;
use yii\db\Transaction;

/**
 * A utility class for managing database transactions with a consistent and safe approach.
 *
 * This class simplifies the process of wrapping database operations within transactions,
 * ensuring that changes are either fully committed or completely rolled back in case of errors.
 *
 * It provides two main methods:
 *
 * - {@see TransactionManager::safeWrap()} - executes a callable within a transaction, safely handling
 *   exceptions and logging them.
 * - {@see TransactionManager::wrap()} - executes a callable within a transaction.
 *
 * Usage examples:
 *
 * ```
 * $transactionResult = $this->transactionManager->safeWrap(function () use ($product) {
 *     $this->productRepository->delete($product);
 *     $this->productFilesystem->delete($product->preview_filename);
 *
 *     return [
 *         'success' => true,
 *     ];
 * });
 * ```
 *
 * ```
 * $this->transactionManager->wrap(function () use ($product) {
 *     $this->productRepository->delete($product);
 *     $this->productFilesystem->delete($product->preview_filename);
 *
 *     return [
 *         'success' => true,
 *     ];
 * });
 * ```
 *
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 *
 * @immutable
 */
class TransactionManager
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
