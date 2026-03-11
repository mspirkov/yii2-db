<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\ActiveRecord;

use Exception;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\ActiveQueryTrait;
use yii\db\BaseActiveRecord;
use yii\db\ExpressionInterface;
use yii\db\Query;
use yii\db\TableSchema;

/**
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 *
 * @template T of ActiveRecord
 */
interface RepositoryInterface
{
    /**
     * Finds a single ActiveRecord model based on the provided condition.
     *
     * @param mixed $condition The condition to search for. This can be a scalar value
     * (e.g., primary key), an array, or an `ExpressionInterface` object. See
     * {@see BaseActiveRecord::findOne()} for more information.
     *
     * @return T|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public function findOne($condition): ?ActiveRecord;

    /**
     * Finds a single ActiveRecord model based on the provided condition and eager loads
     * the specified relations.
     *
     * @param string|array<array-key, mixed>|ExpressionInterface $condition The condition
     * to search for. See {@see Query::where()} for more information.
     * @param string|array<array-key, mixed> $with The relations to eager load. This can
     * be a single relation name or an array. See {@see ActiveQueryTrait::with()} for
     * more information.
     *
     * @return T|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public function findOneWith($condition, $with): ?ActiveRecord;

    /**
     * Finds all ActiveRecord models based on the provided condition.
     *
     * @param mixed $condition The condition to search for. This can be a scalar
     * value (e.g., primary key), an array, or an `ExpressionInterface` object.
     * See {@see BaseActiveRecord::findAll()} for more information.
     *
     * Note: If null, all models are returned.
     *
     * @return T[] An array of ActiveRecord model instances.
     */
    public function findAll($condition = null): array;

    /**
     * Finds all ActiveRecord models based on the provided condition and eager loads
     * the specified relations.
     *
     * @param string|array<array-key, mixed>|ExpressionInterface|null $condition The condition
     * to search for. See {@see Query::where()} for more information.
     *
     * Note: If null, all models are returned.
     * @param string|array<array-key, mixed> $with The relations to eager load. This can
     * be a single relation name or an array. See {@see ActiveQueryTrait::with()} for
     * more information.
     *
     * @return T[] An array of ActiveRecord model instances.
     */
    public function findAllWith($condition, $with): array;

    /**
     * Saves an ActiveRecord model to the database.
     *
     * @param T $model The ActiveRecord model to save.
     * @param bool $runValidation Whether to perform validation before saving. If the
     * validation fails, the record will not be saved to the database and this method
     * will return `false`.
     * @param string[]|null $attributeNames List of attribute names that need to be saved.
     * Defaults to `null`, meaning all attributes that are loaded from DB will be saved.
     *
     * @throws Exception In case update or insert failed.
     *
     * @return bool Whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function save(ActiveRecord $model, bool $runValidation = true, ?array $attributeNames = null): bool;

    /**
     * Deletes an ActiveRecord model from the database.
     *
     * @param T $model The ActiveRecord model to delete.
     *
     * @throws Throwable In case delete failed.
     *
     * @return int|false The number of rows deleted, or `false` if the deletion is unsuccessful
     * for some reason. See {@see ActiveRecord::delete()} for more information.
     */
    public function delete(ActiveRecord $model);

    /**
     * Updates the whole table using the provided attribute values and conditions.
     *
     * @param array<string, mixed> $attributes Attribute values (name-value pairs) to be saved
     * into the table.
     * @param string|array<array-key, mixed> $condition The conditions that will be put in the
     * WHERE part of the UPDATE SQL. See {@see ActiveRecord::updateAll()} for
     * more information.
     * @param array<string, mixed> $params The parameters (name => value) to be bound to the query.
     *
     * @return int The number of rows updated.
     */
    public function updateAll(array $attributes, $condition = '', array $params = []): int;

    /**
     * Deletes rows in the table using the provided conditions.
     *
     * @param string|array<array-key, mixed>|null $condition The conditions that will be put
     * in the WHERE part of the DELETE SQL. See {@see ActiveRecord::deleteAll()} for
     * more information.
     * @param array<string, mixed> $params The parameters (name => value) to be bound to the query.
     *
     * @return int The number of rows deleted.
     */
    public function deleteAll($condition = null, array $params = []): int;

    /**
     * Returns the schema information of the DB table associated with current ActiveRecord class.
     *
     * @throws InvalidConfigException If the table for the current ActiveRecord class does not exist.
     *
     * @return TableSchema The schema information of the DB table associated with this ActiveRecord class.
     */
    public function getTableSchema(): TableSchema;
}
