<?php

declare(strict_types=1);

namespace App\CurrencyRates\Domain\Exception;

final class InvalidExchangeRateException extends \InvalidArgumentException
{
    public static function forValue(string $value): self
    {
        return new self(\sprintf('Invalid exchange rate "%s". Expected a positive decimal number.', $value));
    }
}
