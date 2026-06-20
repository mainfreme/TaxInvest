<?php

declare(strict_types=1);

namespace App\DataImport\Application\Handler;

use App\DataImport\Application\Message\ProcessImportChunkMessage;
use App\DataImport\Application\Message\StartImportMessage;
use App\DataImport\Application\Service\ImportChunkSplitter;
use App\DataImport\Application\Service\ImportFileReaderInterface;
use App\DataImport\Domain\Repository\ImportJobRepositoryInterface;
use App\DataImport\Domain\ValueObject\ImportJobId;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class StartImportHandler
{
    public function __construct(
        private ImportJobRepositoryInterface $importJobRepository,
        private ImportFileReaderInterface $importFileReader,
        private ImportChunkSplitter $chunkSplitter,
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(StartImportMessage $message): void
    {
        $importJobId = ImportJobId::fromString($message->importJobId);
        $importJob = $this->importJobRepository->findById($importJobId);

        if ($importJob === null) {
            $this->logger->error('Import job not found.', ['importJobId' => $message->importJobId]);

            return;
        }

        try {
            $sheets = $this->importFileReader->read(
                $importJob->getFilePath(),
                $importJob->getImportType()->value,
            );

            $messages = [];
            $totalChunks = 0;

            foreach ($sheets as $sheetData) {
                $chunks = $this->chunkSplitter->split($sheetData->rows);
                $sheetTotalChunks = \count($chunks);
                $totalChunks += $sheetTotalChunks;

                foreach ($chunks as $index => $chunkRows) {
                    $messages[] = new ProcessImportChunkMessage(
                        importJobId: $importJobId->toString(),
                        sheet: $sheetData->sheet,
                        chunkNumber: $index + 1,
                        totalChunks: 0,
                        startRowNumber: ($index * $this->chunkSplitter->getChunkSize()) + 1,
                        headers: $sheetData->headers,
                        rows: $chunkRows,
                    );
                }
            }

            $messages = $this->assignTotalChunks($messages, $totalChunks);
            $importJob->markProcessing($totalChunks);
            $this->importJobRepository->save($importJob);

            foreach ($messages as $chunkMessage) {
                $this->messageBus->dispatch($chunkMessage);
            }

            $this->logger->info('Import orchestration completed.', [
                'importJobId' => $importJobId->toString(),
                'totalChunks' => $totalChunks,
            ]);
        } catch (\Throwable $exception) {
            $importJob->markFailed($exception->getMessage());
            $this->importJobRepository->save($importJob);

            $this->logger->error('Import orchestration failed.', [
                'importJobId' => $importJobId->toString(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * @param list<ProcessImportChunkMessage> $messages
     *
     * @return list<ProcessImportChunkMessage>
     */
    private function assignTotalChunks(array $messages, int $totalChunks): array
    {
        return \array_map(
            static fn (ProcessImportChunkMessage $message): ProcessImportChunkMessage => new ProcessImportChunkMessage(
                importJobId: $message->importJobId,
                sheet: $message->sheet,
                chunkNumber: $message->chunkNumber,
                totalChunks: $totalChunks,
                startRowNumber: $message->startRowNumber,
                headers: $message->headers,
                rows: $message->rows,
            ),
            $messages,
        );
    }
}
