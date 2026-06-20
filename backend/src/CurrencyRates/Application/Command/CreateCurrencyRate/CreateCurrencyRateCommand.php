<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Command\CreateCurrencyRate;

final readonly class CreateCurrencyRateCommand
{
    public function __construct(
        public string $baseCurrency,
        public string $targetCurrency,
        public string $rate,
        public string $effectiveDate,
        public ?string $source = null,
    ) {
    }
}
