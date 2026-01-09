<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\DateTimeBehavior;

use MSpirkov\Yii2\Db\ActiveRecord\DateTimeBehavior;

class MessageWithCustomTimeZone extends Message
{
    public const TIME_ZONE = 'Europe/Moscow';

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
