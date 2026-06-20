<?php

declare(strict_types=1);

namespace App\CurrencyRates\Domain\ValueObject;

use App\CurrencyRates\Domain\Exception\InvalidCurrencyCodeException;

final readonly class CurrencyCode
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = \strtoupper(\trim($value));

        if (!\preg_match('/^[A-Z]{3}$/', $normalized)) {
            throw InvalidCurrencyCodeException::forValue($value);
        }

        $this->value = $normalized;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
