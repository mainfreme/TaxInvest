<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Query\GetCurrencyRate;

use App\CurrencyRates\Application\Dto\CurrencyRateView;
use App\CurrencyRates\Application\Mapper\CurrencyRateViewMapper;
use App\CurrencyRates\Domain\Exception\CurrencyRateNotFoundException;
use App\CurrencyRates\Domain\Repository\CurrencyRateRepositoryInterface;
use App\CurrencyRates\Domain\ValueObject\CurrencyRateId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetCurrencyRateHandler
{
    public function __construct(
        private CurrencyRateRepositoryInterface $repository,
        private CurrencyRateViewMapper $viewMapper,
    ) {
    }

    public function __invoke(GetCurrencyRateQuery $query): CurrencyRateView
    {
        $id = CurrencyRateId::fromString($query->id);
        $currencyRate = $this->repository->findById($id);

        if ($currencyRate === null) {
            throw CurrencyRateNotFoundException::withId($id);
        }

        return $this->viewMapper->map($currencyRate);
    }
}
