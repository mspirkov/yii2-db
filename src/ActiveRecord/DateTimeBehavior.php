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
 * By default, this behavior uses the current date, time, and time zone. If necessary, you can specify your own
 * attributes and time zone.
 *
 * @author Maksim Spirkov <spirkov.2001@mail.ru>
 *
 * @template T of BaseActiveRecord
 *
 * @extends AttributeBehavior<T>
 */
class DateTimeBehavior extends AttributeBehavior
{
    public const DATETIME_DB_FORMAT = 'Y-m-d H:i:s';

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
