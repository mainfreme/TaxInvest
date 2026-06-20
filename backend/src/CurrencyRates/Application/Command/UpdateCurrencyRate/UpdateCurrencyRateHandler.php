<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Command\UpdateCurrencyRate;

use App\CurrencyRates\Application\Dto\CurrencyRateView;
use App\CurrencyRates\Application\Mapper\CurrencyRateViewMapper;
use App\CurrencyRates\Domain\Exception\CurrencyRateNotFoundException;
use App\CurrencyRates\Domain\Repository\CurrencyRateRepositoryInterface;
use App\CurrencyRates\Domain\ValueObject\CurrencyRateId;
use App\CurrencyRates\Domain\ValueObject\ExchangeRate;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateCurrencyRateHandler
{
    public function __construct(
        private CurrencyRateRepositoryInterface $repository,
        private CurrencyRateViewMapper $viewMapper,
    ) {
    }

    public function __invoke(UpdateCurrencyRateCommand $command): CurrencyRateView
    {
        $id = CurrencyRateId::fromString($command->id);
        $currencyRate = $this->repository->findById($id);

        if ($currencyRate === null) {
            throw CurrencyRateNotFoundException::withId($id);
        }

        $currencyRate->update(
            rate: new ExchangeRate($command->rate),
            source: $command->source,
        );

        $this->repository->save($currencyRate);

        return $this->viewMapper->map($currencyRate);
    }
}
