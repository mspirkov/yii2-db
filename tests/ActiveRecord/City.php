<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property-read Country $country
 */
class City extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{cities}}';
    }

    /**
     * @return ActiveQuery<Country>
     */
    public function getCountry(): ActiveQuery
    {
        return $this->hasOne(Country::class, ['id' => 'country_id']);
    }
}
