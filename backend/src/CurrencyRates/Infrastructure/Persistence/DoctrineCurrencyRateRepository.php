<?php

declare(strict_types=1);

namespace App\CurrencyRates\Infrastructure\Persistence;

use App\CurrencyRates\Domain\Model\CurrencyRate;
use App\CurrencyRates\Domain\Repository\CurrencyRateRepositoryInterface;
use App\CurrencyRates\Domain\ValueObject\CurrencyCode;
use App\CurrencyRates\Domain\ValueObject\CurrencyRateId;
use App\CurrencyRates\Infrastructure\Persistence\Doctrine\Entity\CurrencyRateRecord;
use App\CurrencyRates\Infrastructure\Persistence\Doctrine\Repository\CurrencyRateRecordRepository;
use App\CurrencyRates\Infrastructure\Persistence\Mapper\CurrencyRatePersistenceMapper;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineCurrencyRateRepository implements CurrencyRateRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CurrencyRateRecordRepository $recordRepository,
        private CurrencyRatePersistenceMapper $mapper,
    ) {
    }

    public function save(CurrencyRate $currencyRate): void
    {
        $existing = $this->recordRepository->find($currencyRate->getId()->toUuid());
        $record = $this->mapper->toRecord($currencyRate, $existing instanceof CurrencyRateRecord ? $existing : null);

        $this->entityManager->persist($record);
        $this->entityManager->flush();
    }

    public function findById(CurrencyRateId $id): ?CurrencyRate
    {
        $record = $this->recordRepository->find($id->toUuid());

        if (!$record instanceof CurrencyRateRecord) {
            return null;
        }

        return $this->mapper->toDomain($record);
    }

    public function findAll(
        ?CurrencyCode $baseCurrency = null,
        ?CurrencyCode $targetCurrency = null,
        ?\DateTimeImmutable $effectiveDate = null,
        int $limit = 50,
        int $offset = 0,
    ): array {
        $qb = $this->recordRepository->createQueryBuilder('cr')
            ->orderBy('cr.effectiveDate', 'DESC')
            ->addOrderBy('cr.baseCurrency', 'ASC')
            ->addOrderBy('cr.targetCurrency', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($baseCurrency !== null) {
            $qb
                ->andWhere('cr.baseCurrency = :baseCurrency')
                ->setParameter('baseCurrency', $baseCurrency->toString());
        }

        if ($targetCurrency !== null) {
            $qb
                ->andWhere('cr.targetCurrency = :targetCurrency')
                ->setParameter('targetCurrency', $targetCurrency->toString());
        }

        if ($effectiveDate !== null) {
            $qb
                ->andWhere('cr.effectiveDate = :effectiveDate')
                ->setParameter('effectiveDate', $effectiveDate, 'date_immutable');
        }

        /** @var list<CurrencyRateRecord> $records */
        $records = $qb->getQuery()->getResult();

        return \array_map(
            fn (CurrencyRateRecord $record): CurrencyRate => $this->mapper->toDomain($record),
            $records,
        );
    }
}
