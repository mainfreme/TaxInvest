<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Query\ListCurrencyRates;

final readonly class ListCurrencyRatesQuery
{
    public function __construct(
        public ?string $baseCurrency = null,
        public ?string $targetCurrency = null,
        public ?string $effectiveDate = null,
        public int $limit = 50,
        public int $offset = 0,
    ) {
    }
}
