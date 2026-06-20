<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Persistence;

use App\DataImport\Domain\Repository\EtoroAccountActivityRepositoryInterface;
use App\DataImport\Infrastructure\Persistence\Doctrine\Entity\EtoroAccountActivityRecord;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final class DoctrineEtoroAccountActivityRepository implements EtoroAccountActivityRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function saveBatch(array $activities): void
    {
        foreach ($activities as $activity) {
            $record = new EtoroAccountActivityRecord();
            $record->setId(Uuid::fromString($activity->getId()));
            $record->setImportJobId(Uuid::fromString($activity->getImportJobId()->toString()));
            $record->setRowHash($activity->getRowHash());
            $record->setRowNumber($activity->getRowNumber());
            $record->setData($activity->getData());

            try {
                $this->entityManager->persist($record);
                $this->entityManager->flush();
            } catch (UniqueConstraintViolationException) {
                $this->entityManager->clear();
            }
        }
    }
}
