<?php

declare(strict_types=1);

namespace App\CurrencyRates\Domain\ValueObject;

use Symfony\Component\Uid\Uuid;

final readonly class CurrencyRateId
{
    private Uuid $value;

    public function __construct(?Uuid $value = null)
    {
        $this->value = $value ?? Uuid::v7();
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }

    public function toString(): string
    {
        return $this->value->toRfc4122();
    }

    public function toUuid(): Uuid
    {
        return $this->value;
    }
}
