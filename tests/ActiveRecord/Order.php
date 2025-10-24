<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Extensions\Db\Tests\ActiveRecord;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $customer_id
 */
class Order extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{orders}}';
    }
}
