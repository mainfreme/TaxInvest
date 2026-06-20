<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Mapper;

use App\CurrencyRates\Application\Dto\CurrencyRateView;
use App\CurrencyRates\Domain\Model\CurrencyRate;

final class CurrencyRateViewMapper
{
    public function map(CurrencyRate $currencyRate): CurrencyRateView
    {
        return new CurrencyRateView(
            id: $currencyRate->getId()->toString(),
            baseCurrency: $currencyRate->getBaseCurrency()->toString(),
            targetCurrency: $currencyRate->getTargetCurrency()->toString(),
            rate: $currencyRate->getRate()->toString(),
            effectiveDate: $currencyRate->getEffectiveDate()->format('Y-m-d'),
            source: $currencyRate->getSource(),
            createdAt: $currencyRate->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $currencyRate->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * @param list<CurrencyRate> $currencyRates
     *
     * @return list<CurrencyRateView>
     */
    public function mapCollection(array $currencyRates): array
    {
        return \array_map(
            fn (CurrencyRate $currencyRate): CurrencyRateView => $this->map($currencyRate),
            $currencyRates,
        );
    }
}
