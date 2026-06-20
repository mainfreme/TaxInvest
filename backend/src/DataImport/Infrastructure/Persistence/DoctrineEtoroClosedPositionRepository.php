<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Persistence;

use App\DataImport\Domain\Model\Etoro\EtoroClosedPosition;
use App\DataImport\Domain\Repository\EtoroClosedPositionRepositoryInterface;
use App\DataImport\Infrastructure\Persistence\Doctrine\Entity\EtoroClosedPositionRecord;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final class DoctrineEtoroClosedPositionRepository implements EtoroClosedPositionRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function saveBatch(array $positions): void
    {
        foreach ($positions as $position) {
            $record = new EtoroClosedPositionRecord();
            $record->setId(Uuid::fromString($position->getId()));
            $record->setImportJobId(Uuid::fromString($position->getImportJobId()->toString()));
            $record->setRowHash($position->getRowHash());
            $record->setRowNumber($position->getRowNumber());
            $record->setData($position->getData());

            try {
                $this->entityManager->persist($record);
                $this->entityManager->flush();
            } catch (UniqueConstraintViolationException) {
                $this->entityManager->clear();
            }
        }
    }
}
