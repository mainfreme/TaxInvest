<?php

declare(strict_types=1);

namespace App\Tests\Unit\CurrencyRates\Domain\ValueObject;

use App\CurrencyRates\Domain\ValueObject\CurrencyRateId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class CurrencyRateIdTest extends TestCase
{
    #[Test]
    public function itGeneratesUuidWhenNotProvided(): void
    {
        $id = new CurrencyRateId();

        self::assertNotEmpty($id->toString());
        self::assertTrue(Uuid::isValid($id->toString()));
    }

    #[Test]
    public function itCreatesFromString(): void
    {
        $uuid = Uuid::v7()->toRfc4122();

        $id = CurrencyRateId::fromString($uuid);

        self::assertSame($uuid, $id->toString());
    }

    #[Test]
    public function itExposesUnderlyingUuid(): void
    {
        $uuid = Uuid::v7();
        $id = new CurrencyRateId($uuid);

        self::assertTrue($uuid->equals($id->toUuid()));
    }
}
