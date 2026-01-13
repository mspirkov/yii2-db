<?php

declare(strict_types=1);

namespace MSpirkov\Yii2\Db\Tests\ActiveRecord;

use DateTimeImmutable;
use DateTimeZone;
use MSpirkov\Yii2\Db\Tests\AbstractTestCase;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\DateTimeBehavior\Message;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\DateTimeBehavior\MessageWithCustomTimeZone;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\DateTimeBehavior\Visit;
use MSpirkov\Yii2\Db\Tests\ActiveRecord\Models\DateTimeBehavior\VisitWithCustomValue;
use Yii;

class DateTimeBehaviorTest extends AbstractTestCase
{
    private const DATETIME_DB_FORMAT = 'Y-m-d H:i:s';

    public function testCreateAndUpdateByDefault(): void
    {
        $dateTimeZone = new DateTimeZone(Yii::$app->timeZone);

        $timeBeforeCreating = time();

        $message = new Message();
        $message->content = 'Test';
        $message->save();

        $timeAfterCreating = time();

        $createdAt = $message->created_at;
        $createdAtDateTime = new DateTimeImmutable($createdAt, $dateTimeZone);
        $createdAtTimestamp = $createdAtDateTime->getTimestamp();

        self::assertSame($createdAt, $createdAtDateTime->format(self::DATETIME_DB_FORMAT));
        self::assertTrue($createdAtTimestamp >= $timeBeforeCreating);
        self::assertTrue($createdAtTimestamp <= $timeAfterCreating);
        self::assertNull($message->updated_at);

        $timeBeforeUpdating = time();

        $message->content = 'Test test';
        $message->save();

        $timeAfterUpdating = time();

        /** @var string|null */
        $updatedAt = $message->updated_at;
        self::assertIsString($updatedAt);

        $updatedAtDateTime = new DateTimeImmutable($updatedAt, $dateTimeZone);
        $updatedAtTimestamp = $updatedAtDateTime->getTimestamp();

        self::assertTrue($updatedAtTimestamp >= $timeBeforeUpdating);
        self::assertTrue($updatedAtTimestamp <= $timeAfterUpdating);
        self::assertSame($updatedAt, $updatedAtDateTime->format(self::DATETIME_DB_FORMAT));
        self::assertSame($createdAt, $message->created_at);
    }

    public function testCreateAndUpdateWithCustomTimeZone(): void
    {
        $dateTimeZone = new DateTimeZone(MessageWithCustomTimeZone::TIME_ZONE);

        $timeBeforeCreating = time();

        $message = new MessageWithCustomTimeZone();
        $message->content = 'Test';
        $message->save();

        $timeAfterCreating = time();

        $createdAt = $message->created_at;
        $createdAtDateTime = new DateTimeImmutable($createdAt, $dateTimeZone);
        $createdAtTimestamp = $createdAtDateTime->getTimestamp();

        self::assertSame($createdAt, $createdAtDateTime->format(self::DATETIME_DB_FORMAT));
        self::assertTrue($createdAtTimestamp >= $timeBeforeCreating);
        self::assertTrue($createdAtTimestamp <= $timeAfterCreating);
        self::assertNull($message->updated_at);

        $timeBeforeUpdating = time();

        $message->content = 'Test test';
        $message->save();

        $timeAfterUpdating = time();

        /** @var string|null */
        $updatedAt = $message->updated_at;
        self::assertIsString($updatedAt);

        $updatedAtDateTime = new DateTimeImmutable($updatedAt, $dateTimeZone);
        $updatedAtTimestamp = $updatedAtDateTime->getTimestamp();

        self::assertTrue($updatedAtTimestamp >= $timeBeforeUpdating);
        self::assertTrue($updatedAtTimestamp <= $timeAfterUpdating);
        self::assertSame($updatedAt, $updatedAtDateTime->format(self::DATETIME_DB_FORMAT));
        self::assertSame($createdAt, $message->created_at);
    }

    public function testCreateWithCustomAttributes(): void
    {
        $dateTimeZone = new DateTimeZone(Yii::$app->timeZone);

        $timeBeforeCreating = time();

        $visit = new Visit();
        $visit->username = 'abcd';
        $visit->save();

        $timeAfterCreating = time();

        $createdAtDateTime = new DateTimeImmutable($visit->created_at, $dateTimeZone);
        $createdAtTimestamp = $createdAtDateTime->getTimestamp();

        self::assertSame($visit->created_at, $createdAtDateTime->format(self::DATETIME_DB_FORMAT));
        self::assertTrue($createdAtTimestamp >= $timeBeforeCreating);
        self::assertTrue($createdAtTimestamp <= $timeAfterCreating);
    }

    public function testCreateWithCustomValue(): void
    {
        $visit = new VisitWithCustomValue();
        $visit->username = 'abcd';
        $visit->save();

        self::assertSame(VisitWithCustomValue::CUSTOM_CREATED_AT_VALUE, $visit->created_at);
    }
}
