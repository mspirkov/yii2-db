<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord\Repositories;

use MSpirkov\Yii2\Db\ActiveRecord\AbstractRepository;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\AbstractRepository\City;

/**
 * @extends AbstractRepository<City>
 */
class CityRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(City::class);
    }
}
