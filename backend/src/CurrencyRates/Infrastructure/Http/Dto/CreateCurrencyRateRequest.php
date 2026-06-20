<?php

declare(strict_types=1);

namespace App\CurrencyRates\Infrastructure\Http\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateCurrencyRateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^[A-Za-z]{3}$/', message: 'Base currency must be a 3-letter ISO 4217 code.')]
        public string $baseCurrency,
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^[A-Za-z]{3}$/', message: 'Target currency must be a 3-letter ISO 4217 code.')]
        public string $targetCurrency,
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d+)?$/', message: 'Rate must be a positive decimal number.')]
        public string $rate,
        #[Assert\NotBlank]
        #[Assert\Date]
        public string $effectiveDate,
        #[Assert\Length(max: 255)]
        public ?string $source = null,
    ) {
    }
}
