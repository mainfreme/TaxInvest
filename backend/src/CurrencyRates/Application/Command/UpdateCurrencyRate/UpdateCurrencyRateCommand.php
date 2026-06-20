<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Command\UpdateCurrencyRate;

final readonly class UpdateCurrencyRateCommand
{
    public function __construct(
        public string $id,
        public string $rate,
        public ?string $source = null,
    ) {
    }
}
