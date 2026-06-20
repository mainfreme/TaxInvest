<?php

declare(strict_types=1);

namespace App\Tests\Unit\CurrencyRates\Domain\ValueObject;

use App\CurrencyRates\Domain\Exception\InvalidExchangeRateException;
use App\CurrencyRates\Domain\ValueObject\ExchangeRate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExchangeRateTest extends TestCase
{
    #[Test]
    #[DataProvider('validExchangeRateProvider')]
    public function itAcceptsValidExchangeRate(string $value, string $expected): void
    {
        $rate = new ExchangeRate($value);

        self::assertSame($expected, $rate->toString());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function validExchangeRateProvider(): iterable
    {
        yield 'integer' => ['4', '4'];
        yield 'decimal' => ['4.25', '4.25'];
        yield 'with whitespace' => [' 4.10 ', '4.10'];
    }

    #[Test]
    #[DataProvider('invalidExchangeRateProvider')]
    public function itRejectsInvalidExchangeRate(string $value): void
    {
        $this->expectException(InvalidExchangeRateException::class);

        new ExchangeRate($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidExchangeRateProvider(): iterable
    {
        yield 'negative' => ['-1'];
        yield 'zero' => ['0'];
        yield 'zero decimal' => ['0.00'];
        yield 'non numeric' => ['abc'];
        yield 'empty string' => [''];
    }
}
