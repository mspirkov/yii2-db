<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\AbstractRepository;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 */
class PaymentSystem extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{payment_systems}}';
    }
}
