<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 */
class Country extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{countries}}';
    }
}
