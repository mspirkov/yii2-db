<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord\Repositories;

use MSpirkov\Yii2\Db\ActiveRecord\AbstractRepository;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\AbstractRepository\PaymentSystem;

/**
 * @extends AbstractRepository<PaymentSystem>
 */
class PaymentSystemRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(PaymentSystem::class);
    }
}
