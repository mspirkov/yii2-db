<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Extensions\Db\ActiveRecord;

use Exception;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\ExpressionInterface;
use yii\db\StaleObjectException;
use yii\db\TableSchema;

/**
 * An abstract class for creating repositories that interact with ActiveRecord models.
 *
 * @template T of ActiveRecord
 *
 * @immutable
 */
abstract class AbstractRepository
{
    /**
     * The fully qualified name of the ActiveRecord model class that this repository manages.
     *
     * @var class-string<T>
     */
    protected string $modelClass;

    /**
     * @param class-string<T> $modelClass The fully qualified name of the ActiveRecord model class.
     */
    protected function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * Finds a single ActiveRecord model based on the provided condition.
     *
     * @param mixed $condition The condition to search for. This can be a scalar value
     * (e.g., primary key), an array, or an `ExpressionInterface` object. See
     * {@link \yii\db\BaseActiveRecord::findOne()} for more information.
     *
     * @return T|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public function findOne($condition): ?ActiveRecord
    {
        return $this->modelClass::findOne($condition);
    }

    /**
     * Finds a single ActiveRecord model based on the provided condition and eager loads
     * the specified relations.
     *
     * @param string|array<array-key, mixed>|ExpressionInterface $condition The condition
     * to search for. See {@link \yii\db\Query::where()} for more information.
     * @param string|array<array-key, mixed> $with The relations to eager load. This can
     * be a single relation name or an array. See {@link \yii\db\ActiveQueryTrait::with()} for
     * more information.
     *
     * @return T|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public function findOneWith($condition, $with): ?ActiveRecord
    {
        return $this->find()->with($with)->where($condition)->one();
    }

    /**
     * Finds all ActiveRecord models based on the provided condition.
     *
     * @param mixed $condition The condition to search for. This can be a scalar
     * value (e.g., primary key), an array, or an `ExpressionInterface` object.
     * See {@link \yii\db\BaseActiveRecord::findAll()} for more information.
     *
     * Note: If null, all models are returned.
     *
     * @return T[] An array of ActiveRecord model instances.
     */
    public function findAll($condition = null): array
    {
        return $condition === null ? $this->find()->all() : $this->modelClass::findAll($condition);
    }

    /**
     * Finds all ActiveRecord models based on the provided condition and eager loads
     * the specified relations.
     *
     * @param string|array<array-key, mixed>|ExpressionInterface|null $condition The condition
     * to search for. See {@link \yii\db\Query::where()} for more information.
     *
     * Note: If null, all models are returned.
     * @param string|array<array-key, mixed> $with The relations to eager load. This can
     * be a single relation name or an array. See {@link \yii\db\ActiveQueryTrait::with()} for
     * more information.
     *
     * @return T[] An array of ActiveRecord model instances.
     */
    public function findAllWith($condition, $with): array
    {
        $query = $this->find()->with($with);

        if ($condition !== null) {
            $query->where($condition);
        }

        return $query->all();
    }

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
    public function save(
        ActiveRecord $model,
        bool $runValidation = true,
        ?array $attributeNames = null
    ): bool {
        return $model->save($runValidation, $attributeNames);
    }

    /**
     * Deletes an ActiveRecord model from the database.
     *
     * @param T $model The ActiveRecord model to delete.
     *
     * @throws StaleObjectException If optimistic locking is enabled and the data being deleted
     * is outdated.
     * @throws Throwable In case delete failed.
     *
     * @return int|false The number of rows deleted, or `false` if the deletion is unsuccessful
     * for some reason. See {@link \yii\db\ActiveRecord::delete()} for more information.
     */
    public function delete(ActiveRecord $model)
    {
        return $model->delete();
    }

    /**
     * Updates the whole table using the provided attribute values and conditions.
     *
     * @param array<string, mixed> $attributes Attribute values (name-value pairs) to be saved
     * into the table.
     * @param string|array<array-key, mixed> $condition The conditions that will be put in the
     * WHERE part of the UPDATE SQL. See {@link \yii\db\ActiveRecord::updateAll()} for
     * more information.
     * @param array<string, mixed> $params The parameters (name => value) to be bound to the query.
     *
     * @return int The number of rows updated.
     */
    public function updateAll(array $attributes, $condition = '', array $params = []): int
    {
        return $this->modelClass::updateAll($attributes, $condition, $params);
    }

    /**
     * Deletes rows in the table using the provided conditions.
     *
     * @param string|array<array-key, mixed>|null $condition The conditions that will be put
     * in the WHERE part of the DELETE SQL. See {@link \yii\db\ActiveRecord::deleteAll()} for
     * more information.
     * @param array<string, mixed> $params The parameters (name => value) to be bound to the query.
     *
     * @return int The number of rows deleted.
     */
    public function deleteAll($condition = null, array $params = []): int
    {
        return $this->modelClass::deleteAll($condition, $params);
    }

    /**
     * Returns the schema information of the DB table associated with current ActiveRecord class.
     *
     * @throws InvalidConfigException If the table for the current ActiveRecord class does not exist.
     *
     * @return TableSchema The schema information of the DB table associated with this ActiveRecord class.
     */
    public function getTableSchema(): TableSchema
    {
        return $this->modelClass::getTableSchema();
    }

    /**
     * Creates and returns a new ActiveQuery instance for the current ActiveRecord model.
     *
     * @return ActiveQuery<T> The newly created ActiveQuery instance.
     */
    protected function find(): ActiveQuery
    {
        return $this->modelClass::find();
    }
}
