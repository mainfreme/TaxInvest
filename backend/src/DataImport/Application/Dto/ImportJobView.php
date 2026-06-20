<?php

declare(strict_types=1);

namespace App\DataImport\Application\Dto;

use App\DataImport\Domain\Model\ImportJob;

final readonly class ImportJobView
{
    /**
     * @param list<array{sheet: string, chunk: int, message: string}> $errors
     */
    public function __construct(
        public string $id,
        public string $importType,
        public string $originalFilename,
        public string $status,
        public int $totalChunks,
        public int $processedChunks,
        public array $errors,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    public static function fromDomain(ImportJob $importJob): self
    {
        return new self(
            id: $importJob->getId()->toString(),
            importType: $importJob->getImportType()->value,
            originalFilename: $importJob->getOriginalFilename(),
            status: $importJob->getStatus()->value,
            totalChunks: $importJob->getTotalChunks(),
            processedChunks: $importJob->getProcessedChunks(),
            errors: $importJob->getErrors(),
            createdAt: $importJob->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $importJob->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
