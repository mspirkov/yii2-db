<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\DateTimeBehavior;

use MSpirkov\Yii2\Db\ActiveRecord\DateTimeBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $username
 * @property string $created_at
 */
final class VisitWithCustomValue extends ActiveRecord
{
    public const CUSTOM_CREATED_AT_VALUE = '2026-01-09 19:46:00';

    public static function tableName(): string
    {
        return '{{visits}}';
    }

    /**
     * @return list<class-string|array{class: class-string, ...}>
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => DateTimeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
                'value' => self::CUSTOM_CREATED_AT_VALUE,
            ],
        ];
    }
}
