<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Persistence;

use App\DataImport\Domain\Model\ImportJob;
use App\DataImport\Domain\Repository\ImportJobRepositoryInterface;
use App\DataImport\Domain\ValueObject\ImportJobId;
use App\DataImport\Infrastructure\Persistence\Doctrine\Entity\ImportJobRecord;
use App\DataImport\Infrastructure\Persistence\Doctrine\Repository\ImportJobRecordRepository;
use App\DataImport\Infrastructure\Persistence\Mapper\ImportJobPersistenceMapper;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineImportJobRepository implements ImportJobRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ImportJobRecordRepository $recordRepository,
        private ImportJobPersistenceMapper $mapper,
    ) {
    }

    public function save(ImportJob $importJob): void
    {
        $existing = $this->recordRepository->find($importJob->getId()->toString());
        $record = $this->mapper->toRecord(
            $importJob,
            $existing instanceof ImportJobRecord ? $existing : null,
        );

        $this->entityManager->persist($record);
        $this->entityManager->flush();
    }

    public function findById(ImportJobId $id): ?ImportJob
    {
        $record = $this->recordRepository->find($id->toString());

        if (!$record instanceof ImportJobRecord) {
            return null;
        }

        return $this->mapper->toDomain($record);
    }
}
