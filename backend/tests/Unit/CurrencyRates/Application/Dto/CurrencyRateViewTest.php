<?php

declare(strict_types=1);

namespace App\Tests\Unit\CurrencyRates\Application\Dto;

use App\CurrencyRates\Application\Dto\CurrencyRateView;
use App\CurrencyRates\Application\Mapper\CurrencyRateViewMapper;
use App\CurrencyRates\Domain\Model\CurrencyRate;
use App\CurrencyRates\Domain\ValueObject\CurrencyCode;
use App\CurrencyRates\Domain\ValueObject\ExchangeRate;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CurrencyRateViewTest extends TestCase
{
    #[Test]
    public function itExposesAllReadModelFields(): void
    {
        $view = new CurrencyRateView(
            id: '019abc00-0000-7000-8000-000000000001',
            baseCurrency: 'PLN',
            targetCurrency: 'EUR',
            rate: '4.25',
            effectiveDate: '2025-06-18',
            source: 'NBP',
            createdAt: '2025-06-18T10:00:00+00:00',
            updatedAt: '2025-06-18T10:00:00+00:00',
        );

        self::assertSame('019abc00-0000-7000-8000-000000000001', $view->id);
        self::assertSame('PLN', $view->baseCurrency);
        self::assertSame('EUR', $view->targetCurrency);
        self::assertSame('4.25', $view->rate);
        self::assertSame('2025-06-18', $view->effectiveDate);
        self::assertSame('NBP', $view->source);
        self::assertSame('2025-06-18T10:00:00+00:00', $view->createdAt);
        self::assertSame('2025-06-18T10:00:00+00:00', $view->updatedAt);
    }

    #[Test]
    public function itAllowsNullSource(): void
    {
        $view = new CurrencyRateView(
            id: '019abc00-0000-7000-8000-000000000001',
            baseCurrency: 'USD',
            targetCurrency: 'PLN',
            rate: '4.00',
            effectiveDate: '2025-06-18',
            source: null,
            createdAt: '2025-06-18T10:00:00+00:00',
            updatedAt: '2025-06-18T10:00:00+00:00',
        );

        self::assertNull($view->source);
    }

    #[Test]
    public function itCanBeBuiltFromDomainAggregate(): void
    {
        $currencyRate = CurrencyRate::create(
            baseCurrency: new CurrencyCode('PLN'),
            targetCurrency: new CurrencyCode('EUR'),
            rate: new ExchangeRate('4.25'),
            effectiveDate: new \DateTimeImmutable('2025-06-18'),
            source: 'NBP',
        );

        $view = (new CurrencyRateViewMapper())->map($currencyRate);

        self::assertSame($currencyRate->getId()->toString(), $view->id);
        self::assertSame('PLN', $view->baseCurrency);
        self::assertSame('EUR', $view->targetCurrency);
        self::assertSame('4.25', $view->rate);
        self::assertSame('2025-06-18', $view->effectiveDate);
        self::assertSame('NBP', $view->source);
    }
}
