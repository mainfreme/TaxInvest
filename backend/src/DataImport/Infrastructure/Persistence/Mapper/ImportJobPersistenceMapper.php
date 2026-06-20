<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Persistence\Mapper;

use App\DataImport\Domain\Model\ImportJob;
use App\DataImport\Domain\ValueObject\ImportJobId;
use App\DataImport\Domain\ValueObject\ImportStatus;
use App\DataImport\Domain\ValueObject\ImportType;
use App\DataImport\Infrastructure\Persistence\Doctrine\Entity\ImportJobRecord;
use Symfony\Component\Uid\Uuid;

final class ImportJobPersistenceMapper
{
    public function toDomain(ImportJobRecord $record): ImportJob
    {
        return ImportJob::restore(
            id: new ImportJobId($record->getId()->toRfc4122()),
            importType: ImportType::from($record->getImportType()),
            filePath: $record->getFilePath(),
            originalFilename: $record->getOriginalFilename(),
            status: ImportStatus::from($record->getStatus()),
            totalChunks: $record->getTotalChunks(),
            processedChunks: $record->getProcessedChunks(),
            errors: $record->getErrors(),
            createdAt: $record->getCreatedAt(),
            updatedAt: $record->getUpdatedAt(),
        );
    }

    public function toRecord(ImportJob $importJob, ?ImportJobRecord $existing = null): ImportJobRecord
    {
        $record = $existing ?? new ImportJobRecord();
        $record->setId(Uuid::fromString($importJob->getId()->toString()));
        $record->setImportType($importJob->getImportType()->value);
        $record->setFilePath($importJob->getFilePath());
        $record->setOriginalFilename($importJob->getOriginalFilename());
        $record->setStatus($importJob->getStatus()->value);
        $record->setTotalChunks($importJob->getTotalChunks());
        $record->setProcessedChunks($importJob->getProcessedChunks());
        $record->setErrors($importJob->getErrors());
        $record->setCreatedAt($importJob->getCreatedAt());
        $record->setUpdatedAt($importJob->getUpdatedAt());

        return $record;
    }
}
