<?php

declare(strict_types=1);

namespace App\CurrencyRates\Domain\Model;

use App\CurrencyRates\Domain\ValueObject\CurrencyCode;
use App\CurrencyRates\Domain\ValueObject\CurrencyRateId;
use App\CurrencyRates\Domain\ValueObject\ExchangeRate;

final class CurrencyRate
{
    private CurrencyRateId $id;
    private CurrencyCode $baseCurrency;
    private CurrencyCode $targetCurrency;
    private ExchangeRate $rate;
    private \DateTimeImmutable $effectiveDate;
    private ?string $source;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    private function __construct(
        CurrencyRateId $id,
        CurrencyCode $baseCurrency,
        CurrencyCode $targetCurrency,
        ExchangeRate $rate,
        \DateTimeImmutable $effectiveDate,
        ?string $source,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->baseCurrency = $baseCurrency;
        $this->targetCurrency = $targetCurrency;
        $this->rate = $rate;
        $this->effectiveDate = $effectiveDate;
        $this->source = $source;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        CurrencyCode $baseCurrency,
        CurrencyCode $targetCurrency,
        ExchangeRate $rate,
        \DateTimeImmutable $effectiveDate,
        ?string $source = null,
    ): self {
        $now = new \DateTimeImmutable();

        return new self(
            id: new CurrencyRateId(),
            baseCurrency: $baseCurrency,
            targetCurrency: $targetCurrency,
            rate: $rate,
            effectiveDate: $effectiveDate,
            source: $source,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function restore(
        CurrencyRateId $id,
        CurrencyCode $baseCurrency,
        CurrencyCode $targetCurrency,
        ExchangeRate $rate,
        \DateTimeImmutable $effectiveDate,
        ?string $source,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: $id,
            baseCurrency: $baseCurrency,
            targetCurrency: $targetCurrency,
            rate: $rate,
            effectiveDate: $effectiveDate,
            source: $source,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function update(ExchangeRate $rate, ?string $source = null): void
    {
        $this->rate = $rate;
        $this->source = $source;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): CurrencyRateId
    {
        return $this->id;
    }

    public function getBaseCurrency(): CurrencyCode
    {
        return $this->baseCurrency;
    }

    public function getTargetCurrency(): CurrencyCode
    {
        return $this->targetCurrency;
    }

    public function getRate(): ExchangeRate
    {
        return $this->rate;
    }

    public function getEffectiveDate(): \DateTimeImmutable
    {
        return $this->effectiveDate;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
