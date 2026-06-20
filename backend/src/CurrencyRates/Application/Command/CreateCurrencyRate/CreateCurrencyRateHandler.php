<?php

declare(strict_types=1);

namespace App\CurrencyRates\Application\Command\CreateCurrencyRate;

use App\CurrencyRates\Application\Dto\CurrencyRateView;
use App\CurrencyRates\Application\Mapper\CurrencyRateViewMapper;
use App\CurrencyRates\Domain\Model\CurrencyRate;
use App\CurrencyRates\Domain\Repository\CurrencyRateRepositoryInterface;
use App\CurrencyRates\Domain\ValueObject\CurrencyCode;
use App\CurrencyRates\Domain\ValueObject\ExchangeRate;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateCurrencyRateHandler
{
    public function __construct(
        private CurrencyRateRepositoryInterface $repository,
        private CurrencyRateViewMapper $viewMapper,
    ) {
    }

    public function __invoke(CreateCurrencyRateCommand $command): CurrencyRateView
    {
        $currencyRate = CurrencyRate::create(
            baseCurrency: new CurrencyCode($command->baseCurrency),
            targetCurrency: new CurrencyCode($command->targetCurrency),
            rate: new ExchangeRate($command->rate),
            effectiveDate: new \DateTimeImmutable($command->effectiveDate),
            source: $command->source,
        );

        $this->repository->save($currencyRate);

        return $this->viewMapper->map($currencyRate);
    }
}
