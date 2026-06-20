<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Query\GetCurrencyRate;

final readonly class GetCurrencyRateQuery
{
    public function __construct(
        public string $id,
    ) {
    }
}
