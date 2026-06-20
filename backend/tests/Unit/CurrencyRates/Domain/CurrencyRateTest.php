<?php

declare(strict_types=1);

namespace App\Tests\Unit\CurrencyRates\Domain;

use App\CurrencyRates\Domain\Exception\InvalidCurrencyCodeException;
use App\CurrencyRates\Domain\Exception\InvalidExchangeRateException;
use App\CurrencyRates\Domain\Model\CurrencyRate;
use App\CurrencyRates\Domain\ValueObject\CurrencyCode;
use App\CurrencyRates\Domain\ValueObject\ExchangeRate;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CurrencyRateTest extends TestCase
{
    #[Test]
    public function itCreatesCurrencyRateWithGeneratedId(): void
    {
        $currencyRate = CurrencyRate::create(
            baseCurrency: new CurrencyCode('PLN'),
            targetCurrency: new CurrencyCode('EUR'),
            rate: new ExchangeRate('4.25'),
            effectiveDate: new \DateTimeImmutable('2025-06-18'),
            source: 'NBP',
        );

        self::assertSame('PLN', $currencyRate->getBaseCurrency()->toString());
        self::assertSame('EUR', $currencyRate->getTargetCurrency()->toString());
        self::assertSame('4.25', $currencyRate->getRate()->toString());
        self::assertSame('NBP', $currencyRate->getSource());
        self::assertNotEmpty($currencyRate->getId()->toString());
    }

    #[Test]
    public function itUpdatesRateAndTimestamp(): void
    {
        $currencyRate = CurrencyRate::create(
            baseCurrency: new CurrencyCode('USD'),
            targetCurrency: new CurrencyCode('PLN'),
            rate: new ExchangeRate('4.00'),
            effectiveDate: new \DateTimeImmutable('2025-06-18'),
        );

        $previousUpdatedAt = $currencyRate->getUpdatedAt();

        $currencyRate->update(new ExchangeRate('4.10'), 'ECB');

        self::assertSame('4.10', $currencyRate->getRate()->toString());
        self::assertSame('ECB', $currencyRate->getSource());
        self::assertGreaterThanOrEqual($previousUpdatedAt, $currencyRate->getUpdatedAt());
    }

    #[Test]
    public function itNormalizesCurrencyCodeToUppercase(): void
    {
        $code = new CurrencyCode('eur');

        self::assertSame('EUR', $code->toString());
    }

    #[Test]
    public function itRejectsInvalidCurrencyCode(): void
    {
        $this->expectException(InvalidCurrencyCodeException::class);

        new CurrencyCode('EURO');
    }

    #[Test]
    public function itRejectsInvalidExchangeRate(): void
    {
        $this->expectException(InvalidExchangeRateException::class);

        new ExchangeRate('-1');
    }
}
