<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\DateTimeBehavior;

use MSpirkov\Yii2\Db\ActiveRecord\DateTimeBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $content
 * @property string $created_at
 * @property string|null $updated_at
 */
final class MessageWithCustomTimeZone extends ActiveRecord
{
    public const TIME_ZONE = 'Europe/Moscow';

    public static function tableName(): string
    {
        return '{{messages}}';
    }

    /**
     * @return list<class-string|array{class: class-string, ...}>
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => DateTimeBehavior::class,
                'timeZone' => self::TIME_ZONE,
            ],
        ];
    }
}
