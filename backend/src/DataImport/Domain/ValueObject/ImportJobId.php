<?php

declare(strict_types=1);

namespace App\DataImport\Domain\ValueObject;

final readonly class ImportJobId
{
    public function __construct(
        private string $value,
    ) {
    }

    public static function generate(): self
    {
        return new self(\Symfony\Component\Uid\Uuid::v7()->toRfc4122());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
