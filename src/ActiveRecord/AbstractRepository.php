<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\ActiveRecord;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\TableSchema;

/**
 * An abstract class for creating repositories that interact with ActiveRecord models.
 *
 * Contains the most commonly used methods:
 *
 * - {@see RepositoryInterface::findOne()} - finds a single ActiveRecord model based on the provided condition.
 * - {@see RepositoryInterface::findAll()} - finds all ActiveRecord models based on the provided condition.
 * - {@see RepositoryInterface::save()} - saves an ActiveRecord model to the database.
 * - {@see RepositoryInterface::delete()} - deletes an ActiveRecord model from the database.
 * - {@see RepositoryInterface::updateAll()} - updates the whole table using the provided attribute values and conditions.
 * - {@see RepositoryInterface::deleteAll()} - deletes rows in the table using the provided conditions.
 *
 * It also has several additional methods:
 *
 * - {@see RepositoryInterface::findOneWith()} - finds a single ActiveRecord model based on the provided condition
 *   and eager loads the specified relations.
 * - {@see RepositoryInterface::findAllWith()} - finds all ActiveRecord models based on the provided condition and
 *   eager loads the specified relations.
 * - {@see RepositoryInterface::getTableSchema()} - returns the schema information of the DB table associated with
 *   current ActiveRecord class.
 * - {@see AbstractRepository::find()} - creates and returns a new ActiveQuery instance for the current ActiveRecord model.
 *
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 *
 * @template T of ActiveRecord
 *
 * @implements RepositoryInterface<T>
 *
 * @immutable
 */
abstract class AbstractRepository implements RepositoryInterface
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

    public function findOne($condition): ?ActiveRecord
    {
        return $this->modelClass::findOne($condition);
    }

    public function findOneWith($condition, $with): ?ActiveRecord
    {
        return $this->find()->with($with)->where($condition)->one();
    }

    public function findAll($condition = null): array
    {
        return $condition === null ? $this->find()->all() : $this->modelClass::findAll($condition);
    }

    public function findAllWith($condition, $with): array
    {
        $query = $this->find()->with($with);

        if ($condition !== null) {
            $query->where($condition);
        }

        return $query->all();
    }

    public function save(ActiveRecord $model, bool $runValidation = true, ?array $attributeNames = null): bool
    {
        return $model->save($runValidation, $attributeNames);
    }

    public function delete(ActiveRecord $model)
    {
        return $model->delete();
    }

    public function updateAll(array $attributes, $condition = '', array $params = []): int
    {
        return $this->modelClass::updateAll($attributes, $condition, $params);
    }

    public function deleteAll($condition = null, array $params = []): int
    {
        return $this->modelClass::deleteAll($condition, $params);
    }

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
