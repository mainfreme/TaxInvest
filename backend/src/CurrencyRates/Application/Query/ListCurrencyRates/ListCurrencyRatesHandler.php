<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Query\ListCurrencyRates;

use App\CurrencyRates\Application\Dto\CurrencyRateView;
use App\CurrencyRates\Application\Mapper\CurrencyRateViewMapper;
use App\CurrencyRates\Domain\Repository\CurrencyRateRepositoryInterface;
use App\CurrencyRates\Domain\ValueObject\CurrencyCode;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class ListCurrencyRatesHandler
{
    public function __construct(
        private CurrencyRateRepositoryInterface $repository,
        private CurrencyRateViewMapper $viewMapper,
    ) {
    }

    /**
     * @return list<CurrencyRateView>
     */
    public function __invoke(ListCurrencyRatesQuery $query): array
    {
        $currencyRates = $this->repository->findAll(
            baseCurrency: $query->baseCurrency !== null ? new CurrencyCode($query->baseCurrency) : null,
            targetCurrency: $query->targetCurrency !== null ? new CurrencyCode($query->targetCurrency) : null,
            effectiveDate: $query->effectiveDate !== null ? new \DateTimeImmutable($query->effectiveDate) : null,
            limit: $query->limit,
            offset: $query->offset,
        );

        return $this->viewMapper->mapCollection($currencyRates);
    }
}
