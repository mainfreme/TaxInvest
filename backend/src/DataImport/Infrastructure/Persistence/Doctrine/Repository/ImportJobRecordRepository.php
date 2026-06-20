<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Persistence\Doctrine\Repository;

use App\DataImport\Infrastructure\Persistence\Doctrine\Entity\ImportJobRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImportJobRecord>
 */
class ImportJobRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportJobRecord::class);
    }
}
