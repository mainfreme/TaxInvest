<?php

declare(strict_types=1);

namespace App\CurrencyRates\Domain\Repository;

use App\CurrencyRates\Domain\Model\CurrencyRate;
use App\CurrencyRates\Domain\ValueObject\CurrencyCode;
use App\CurrencyRates\Domain\ValueObject\CurrencyRateId;

interface CurrencyRateRepositoryInterface
{
    public function save(CurrencyRate $currencyRate): void;

    public function findById(CurrencyRateId $id): ?CurrencyRate;

    /**
     * @return list<CurrencyRate>
     */
    public function findAll(
        ?CurrencyCode $baseCurrency = null,
        ?CurrencyCode $targetCurrency = null,
        ?\DateTimeImmutable $effectiveDate = null,
        int $limit = 50,
        int $offset = 0,
    ): array;
}
