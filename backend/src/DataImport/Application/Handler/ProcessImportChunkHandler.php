<?php

declare(strict_types=1);

namespace App\DataImport\Application\Handler;

use App\DataImport\Application\Mapper\EtoroRowMapper;
use App\DataImport\Application\Message\ProcessImportChunkMessage;
use App\DataImport\Domain\Repository\EtoroAccountActivityRepositoryInterface;
use App\DataImport\Domain\Repository\EtoroClosedPositionRepositoryInterface;
use App\DataImport\Domain\Repository\EtoroDividendRepositoryInterface;
use App\DataImport\Domain\Repository\ImportJobRepositoryInterface;
use App\DataImport\Domain\ValueObject\EtoroSheet;
use App\DataImport\Domain\ValueObject\ImportJobId;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProcessImportChunkHandler
{
    public function __construct(
        private ImportJobRepositoryInterface $importJobRepository,
        private EtoroClosedPositionRepositoryInterface $closedPositionRepository,
        private EtoroAccountActivityRepositoryInterface $accountActivityRepository,
        private EtoroDividendRepositoryInterface $dividendRepository,
        private EtoroRowMapper $rowMapper,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ProcessImportChunkMessage $message): void
    {
        $importJobId = ImportJobId::fromString($message->importJobId);
        $importJob = $this->importJobRepository->findById($importJobId);

        if ($importJob === null) {
            $this->logger->error('Import job not found for chunk.', ['importJobId' => $message->importJobId]);

            return;
        }

        try {
            $startRowNumber = $message->startRowNumber;
            $sheet = EtoroSheet::from($message->sheet);

            match ($sheet) {
                EtoroSheet::ClosedPositions => $this->closedPositionRepository->saveBatch(
                    $this->rowMapper->mapClosedPositions($importJobId, $message->headers, $message->rows, $startRowNumber),
                ),
                EtoroSheet::AccountActivity => $this->accountActivityRepository->saveBatch(
                    $this->rowMapper->mapAccountActivities($importJobId, $message->headers, $message->rows, $startRowNumber),
                ),
                EtoroSheet::Dividends => $this->dividendRepository->saveBatch(
                    $this->rowMapper->mapDividends($importJobId, $message->headers, $message->rows, $startRowNumber),
                ),
            };
        } catch (\Throwable $exception) {
            $importJob->addError([
                'sheet' => $message->sheet,
                'chunk' => $message->chunkNumber,
                'message' => $exception->getMessage(),
            ]);
            $this->logger->error('Import chunk failed.', [
                'importJobId' => $message->importJobId,
                'sheet' => $message->sheet,
                'chunk' => $message->chunkNumber,
                'error' => $exception->getMessage(),
            ]);
        }

        $importJob->incrementProcessedChunks();
        $this->importJobRepository->save($importJob);
    }
}
