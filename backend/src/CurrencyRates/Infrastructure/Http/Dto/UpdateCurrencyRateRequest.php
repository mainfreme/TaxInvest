<?php

declare(strict_types=1);

namespace App\CurrencyRates\Infrastructure\Http\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateCurrencyRateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d+(\.\d+)?$/', message: 'Rate must be a positive decimal number.')]
        public string $rate,
        #[Assert\Length(max: 255)]
        public ?string $source = null,
    ) {
    }
}
