<?php

declare(strict_types=1);

namespace App\CurrencyRates\Infrastructure\Persistence\Doctrine\Repository;

use App\CurrencyRates\Infrastructure\Persistence\Doctrine\Entity\CurrencyRateRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CurrencyRateRecord>
 */
class CurrencyRateRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRateRecord::class);
    }
}
