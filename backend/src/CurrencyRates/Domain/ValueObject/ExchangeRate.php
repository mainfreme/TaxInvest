<?php

declare(strict_types=1);

namespace App\CurrencyRates\Domain\ValueObject;

use App\CurrencyRates\Domain\Exception\InvalidExchangeRateException;

final readonly class ExchangeRate
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = \trim($value);

        if (!\preg_match('/^\d+(\.\d+)?$/', $normalized) || (float) $normalized <= 0) {
            throw InvalidExchangeRateException::forValue($value);
        }

        $this->value = $normalized;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
