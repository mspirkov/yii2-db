<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\ActiveRecord;

use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use yii\base\Application;
use DateTimeImmutable;
use DateTimeZone;
use Yii;

/**
 * Behavior for ActiveRecord models that automatically fills the specified attributes with the current date and time.
 *
 * Usage example:
 *
 * ```
 * use MSpirkov\Yii2\Db\ActiveRecord\DateTimeBehavior;
 *
 * public function behaviors(): array
 * {
 *     return [
 *         DateTimeBehavior::class,
 *     ];
 * }
 * ```
 *
 * By default, this behavior will fill the `created_at` attribute with the date and time when the associated
 * AR object is being inserted; it will fill the `updated_at` attribute with the date and time when the AR object
 * is being updated. The date and time are determined relative to {@see DateTimeBehavior::$timeZone}.
 *
 * If your attribute names are different or you want to use a different way of calculating the timestamp,
 * you may configure the {@see DateTimeBehavior::$createdAtAttribute}, {@see DateTimeBehavior::$updatedAtAttribute}
 * and {@see DateTimeBehavior::$value} properties like the following:
 *
 * ```
 * use MSpirkov\Yii2\Db\ActiveRecord\DateTimeBehavior;
 * use yii\db\Expression;
 *
 * public function behaviors(): array
 * {
 *     return [
 *         [
 *             'class' => DateTimeBehavior::class,
 *             'createdAtAttribute' => 'create_time',
 *             'updatedAtAttribute' => 'update_time',
 *             'value' => new Expression('NOW()'),
 *         ],
 *     ];
 * }
 * ```
 *
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 *
 * @template T of BaseActiveRecord
 *
 * @extends AttributeBehavior<T>
 */
class DateTimeBehavior extends AttributeBehavior
{
    private const DATETIME_DB_FORMAT = 'Y-m-d H:i:s';

    /**
     * An attribute that will receive a value when the record is created.
     */
    public string $createdAtAttribute = 'created_at';

    /**
     * An attribute that will receive a value when the record is updated.
     */
    public string $updatedAtAttribute = 'updated_at';

    /**
     * The time zone that will be used if {@see DateTimeBehavior::$value} is not specified. If the
     * value is `null`, the value of {@see Application::$timeZone} will be used.
     */
    public ?string $timeZone = null;

    /**
     * {@inheritdoc}
     *
     * If the value is `null`, the current date and time for the specified time zone will
     * be used (see {@see DateTimeBehavior::$timeZone})
     */
    public $value;

    public function init(): void
    {
        parent::init();

        if ($this->attributes === []) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->createdAtAttribute,
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedAtAttribute,
            ];
        }
    }

    protected function getValue($event)
    {
        if ($this->value === null) {
            $timeZone = $this->timeZone ?? Yii::$app->timeZone;
            $date = new DateTimeImmutable('now', new DateTimeZone($timeZone));

            return $date->format(self::DATETIME_DB_FORMAT);
        }

        return parent::getValue($event);
    }
}
