<?php

declare(strict_types=1);

namespace App\CurrencyRates\Domain\Exception;

final class InvalidCurrencyCodeException extends \InvalidArgumentException
{
    public static function forValue(string $value): self
    {
        return new self(\sprintf('Invalid currency code "%s". Expected 3-letter ISO 4217 code.', $value));
    }
}
