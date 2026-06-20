<?php

declare(strict_types=1);

namespace App\Tests\Unit\CurrencyRates\Domain\ValueObject;

use App\CurrencyRates\Domain\Exception\InvalidCurrencyCodeException;
use App\CurrencyRates\Domain\ValueObject\CurrencyCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CurrencyCodeTest extends TestCase
{
    #[Test]
    public function itNormalizesCurrencyCodeToUppercase(): void
    {
        $code = new CurrencyCode('eur');

        self::assertSame('EUR', $code->toString());
    }

    #[Test]
    public function itTrimsWhitespace(): void
    {
        $code = new CurrencyCode(' pln ');

        self::assertSame('PLN', $code->toString());
    }

    #[Test]
    public function itComparesEqualCodes(): void
    {
        $first = new CurrencyCode('USD');
        $second = new CurrencyCode('usd');

        self::assertTrue($first->equals($second));
    }

    #[Test]
    public function itComparesDifferentCodes(): void
    {
        $first = new CurrencyCode('USD');
        $second = new CurrencyCode('EUR');

        self::assertFalse($first->equals($second));
    }

    #[Test]
    #[DataProvider('invalidCurrencyCodeProvider')]
    public function itRejectsInvalidCurrencyCode(string $value): void
    {
        $this->expectException(InvalidCurrencyCodeException::class);

        new CurrencyCode($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidCurrencyCodeProvider(): iterable
    {
        yield 'too short' => ['EU'];
        yield 'too long' => ['EURO'];
        yield 'contains digits' => ['EU1'];
        yield 'empty string' => [''];
        yield 'whitespace only' => ['   '];
    }
}
