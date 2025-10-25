<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $email
 * @property string $name
 * @property-read Order[] $orders
 */
class Customer extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{customers}}';
    }

    public function rules(): array
    {
        return [
            [['email', 'name'], 'required'],
        ];
    }

    /**
     * @return ActiveQuery<Order>
     */
    public function getOrders(): ActiveQuery
    {
        return $this->hasMany(Order::class, ['customer_id' => 'id']);
    }
}
