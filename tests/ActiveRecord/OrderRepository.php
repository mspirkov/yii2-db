<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Extensions\Db\Tests\ActiveRecord;

use MSpirkov\Yii2\Extensions\Db\ActiveRecord\AbstractRepository;

/**
 * @extends AbstractRepository<Order>
 */
class OrderRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Order::class);
    }
}
