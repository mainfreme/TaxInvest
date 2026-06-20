<?php

declare(strict_types=1);

namespace App\DataImport\Domain\Model\Etoro;

use App\DataImport\Domain\ValueObject\EtoroSheet;
use App\DataImport\Domain\ValueObject\ImportJobId;

final class EtoroDividend
{
    private function __construct(
        private string $id,
        private ImportJobId $importJobId,
        private string $rowHash,
        private int $rowNumber,
        /** @var array<string, string|null> */
        private array $data,
    ) {
    }

    /**
     * @param array<string, string|null> $data
     */
    public static function create(
        ImportJobId $importJobId,
        string $rowHash,
        int $rowNumber,
        array $data,
    ): self {
        return new self(
            id: \Symfony\Component\Uid\Uuid::v7()->toRfc4122(),
            importJobId: $importJobId,
            rowHash: $rowHash,
            rowNumber: $rowNumber,
            data: $data,
        );
    }

    /**
     * @param array<string, string|null> $data
     */
    public static function restore(
        string $id,
        ImportJobId $importJobId,
        string $rowHash,
        int $rowNumber,
        array $data,
    ): self {
        return new self($id, $importJobId, $rowHash, $rowNumber, $data);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getImportJobId(): ImportJobId
    {
        return $this->importJobId;
    }

    public function getRowHash(): string
    {
        return $this->rowHash;
    }

    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }

    /**
     * @return array<string, string|null>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getSheet(): EtoroSheet
    {
        return EtoroSheet::Dividends;
    }
}
