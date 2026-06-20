<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Persistence;

use App\DataImport\Domain\Repository\EtoroDividendRepositoryInterface;
use App\DataImport\Infrastructure\Persistence\Doctrine\Entity\EtoroDividendRecord;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final class DoctrineEtoroDividendRepository implements EtoroDividendRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function saveBatch(array $dividends): void
    {
        foreach ($dividends as $dividend) {
            $record = new EtoroDividendRecord();
            $record->setId(Uuid::fromString($dividend->getId()));
            $record->setImportJobId(Uuid::fromString($dividend->getImportJobId()->toString()));
            $record->setRowHash($dividend->getRowHash());
            $record->setRowNumber($dividend->getRowNumber());
            $record->setData($dividend->getData());

            try {
                $this->entityManager->persist($record);
                $this->entityManager->flush();
            } catch (UniqueConstraintViolationException) {
                $this->entityManager->clear();
            }
        }
    }
}
