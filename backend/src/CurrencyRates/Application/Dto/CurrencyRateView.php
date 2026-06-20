<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Dto;

final readonly class CurrencyRateView
{
    public function __construct(
        public string $id,
        public string $baseCurrency,
        public string $targetCurrency,
        public string $rate,
        public string $effectiveDate,
        public ?string $source,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }
}
