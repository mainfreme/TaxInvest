<?php

declare(strict_types=1);

namespace App\CurrencyRates\Infrastructure\Persistence\Mapper;

use App\CurrencyRates\Domain\Model\CurrencyRate;
use App\CurrencyRates\Domain\ValueObject\CurrencyCode;
use App\CurrencyRates\Domain\ValueObject\CurrencyRateId;
use App\CurrencyRates\Domain\ValueObject\ExchangeRate;
use App\CurrencyRates\Infrastructure\Persistence\Doctrine\Entity\CurrencyRateRecord;

final class CurrencyRatePersistenceMapper
{
    public function toDomain(CurrencyRateRecord $record): CurrencyRate
    {
        return CurrencyRate::restore(
            id: new CurrencyRateId($record->getId()),
            baseCurrency: new CurrencyCode($record->getBaseCurrency()),
            targetCurrency: new CurrencyCode($record->getTargetCurrency()),
            rate: new ExchangeRate($record->getRate()),
            effectiveDate: $record->getEffectiveDate(),
            source: $record->getSource(),
            createdAt: $record->getCreatedAt(),
            updatedAt: $record->getUpdatedAt(),
        );
    }

    public function toRecord(CurrencyRate $currencyRate, ?CurrencyRateRecord $existing = null): CurrencyRateRecord
    {
        $record = $existing ?? new CurrencyRateRecord();
        $record->setId($currencyRate->getId()->toUuid());
        $record->setBaseCurrency($currencyRate->getBaseCurrency()->toString());
        $record->setTargetCurrency($currencyRate->getTargetCurrency()->toString());
        $record->setRate($currencyRate->getRate()->toString());
        $record->setEffectiveDate($currencyRate->getEffectiveDate());
        $record->setSource($currencyRate->getSource());
        $record->setCreatedAt($currencyRate->getCreatedAt());
        $record->setUpdatedAt($currencyRate->getUpdatedAt());

        return $record;
    }
}
