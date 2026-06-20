<?php

declare(strict_types=1);

namespace App\CurrencyRates\Infrastructure\Http\Controller;

use App\CurrencyRates\Application\Command\CreateCurrencyRate\CreateCurrencyRateCommand;
use App\CurrencyRates\Application\Command\UpdateCurrencyRate\UpdateCurrencyRateCommand;
use App\CurrencyRates\Application\Query\GetCurrencyRate\GetCurrencyRateQuery;
use App\CurrencyRates\Application\Query\ListCurrencyRates\ListCurrencyRatesQuery;
use App\CurrencyRates\Domain\Exception\CurrencyRateNotFoundException;
use App\CurrencyRates\Infrastructure\Http\Dto\CreateCurrencyRateRequest;
use App\CurrencyRates\Infrastructure\Http\Dto\UpdateCurrencyRateRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/currency-rates')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class CurrencyRateController extends AbstractController
{
    public function __construct(
        #[Autowire('@command.bus')]
        private MessageBusInterface $commandBus,
        #[Autowire('@query.bus')]
        private MessageBusInterface $queryBus,
    ) {
    }

    #[Route('', name: 'api_currency_rates_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 50);
        $offset = (int) $request->query->get('offset', 0);

        try {
            $result = $this->dispatchQuery(new ListCurrencyRatesQuery(
                baseCurrency: $request->query->getString('baseCurrency') ?: null,
                targetCurrency: $request->query->getString('targetCurrency') ?: null,
                effectiveDate: $request->query->getString('effectiveDate') ?: null,
                limit: \max(1, \min($limit, 100)),
                offset: \max(0, $offset),
            ));
        } catch (HandlerFailedException $exception) {
            return $this->mapException($exception);
        }

        return $this->json([
            'items' => $result,
            'meta' => [
                'limit' => \max(1, \min($limit, 100)),
                'offset' => \max(0, $offset),
                'count' => \count($result),
            ],
        ]);
    }

    #[Route('/{id}', name: 'api_currency_rates_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        try {
            $result = $this->dispatchQuery(new GetCurrencyRateQuery($id));
        } catch (HandlerFailedException $exception) {
            return $this->mapException($exception);
        }

        return $this->json($result);
    }

    #[Route('', name: 'api_currency_rates_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateCurrencyRateRequest $request,
    ): JsonResponse {
        try {
            $result = $this->dispatchCommand(new CreateCurrencyRateCommand(
                baseCurrency: $request->baseCurrency,
                targetCurrency: $request->targetCurrency,
                rate: $request->rate,
                effectiveDate: $request->effectiveDate,
                source: $request->source,
            ));
        } catch (HandlerFailedException $exception) {
            return $this->mapException($exception);
        }

        return $this->json($result, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_currency_rates_update', methods: ['PUT'])]
    public function update(
        string $id,
        #[MapRequestPayload] UpdateCurrencyRateRequest $request,
    ): JsonResponse {
        try {
            $result = $this->dispatchCommand(new UpdateCurrencyRateCommand(
                id: $id,
                rate: $request->rate,
                source: $request->source,
            ));
        } catch (HandlerFailedException $exception) {
            return $this->mapException($exception);
        }

        return $this->json($result);
    }

    private function dispatchCommand(object $command): mixed
    {
        $envelope = $this->commandBus->dispatch($command);

        return $envelope->last(HandledStamp::class)?->getResult();
    }

    private function dispatchQuery(object $query): mixed
    {
        $envelope = $this->queryBus->dispatch($query);

        return $envelope->last(HandledStamp::class)?->getResult();
    }

    private function mapException(HandlerFailedException $exception): JsonResponse
    {
        $previous = $exception->getPrevious();

        if ($previous instanceof CurrencyRateNotFoundException) {
            return $this->json(['message' => $previous->getMessage()], Response::HTTP_NOT_FOUND);
        }

        if ($previous instanceof \InvalidArgumentException) {
            return $this->json(['message' => $previous->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        throw $exception;
    }
}
