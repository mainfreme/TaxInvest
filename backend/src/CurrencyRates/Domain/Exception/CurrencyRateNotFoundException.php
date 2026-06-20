<?php

declare(strict_types=1);

namespace App\CurrencyRates\Domain\Exception;

use App\CurrencyRates\Domain\ValueObject\CurrencyRateId;

final class CurrencyRateNotFoundException extends \RuntimeException
{
    public static function withId(CurrencyRateId $id): self
    {
        return new self(\sprintf('Currency rate with id "%s" was not found.', $id->toString()));
    }
}
