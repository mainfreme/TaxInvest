<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataImport\Domain;

use App\DataImport\Domain\Model\ImportJob;
use App\DataImport\Domain\ValueObject\ImportStatus;
use App\DataImport\Domain\ValueObject\ImportType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ImportJobTest extends TestCase
{
    #[Test]
    public function itCreatesPendingImportJob(): void
    {
        $importJob = ImportJob::create(
            importType: ImportType::EtoroStatement,
            filePath: '/tmp/file.xlsx',
            originalFilename: 'statement.xlsx',
        );

        self::assertSame(ImportStatus::Pending, $importJob->getStatus());
        self::assertSame(0, $importJob->getTotalChunks());
    }

    #[Test]
    public function itMarksProcessingWithChunkCount(): void
    {
        $importJob = ImportJob::create(
            importType: ImportType::EtoroStatement,
            filePath: '/tmp/file.xlsx',
            originalFilename: 'statement.xlsx',
        );

        $importJob->markProcessing(10);

        self::assertSame(ImportStatus::Processing, $importJob->getStatus());
        self::assertSame(10, $importJob->getTotalChunks());
    }

    #[Test]
    public function itCompletesImmediatelyWhenThereAreNoChunks(): void
    {
        $importJob = ImportJob::create(
            importType: ImportType::EtoroStatement,
            filePath: '/tmp/file.xlsx',
            originalFilename: 'statement.xlsx',
        );

        $importJob->markProcessing(0);

        self::assertSame(ImportStatus::Completed, $importJob->getStatus());
    }

    #[Test]
    public function itCompletesAfterAllChunksAreProcessed(): void
    {
        $importJob = ImportJob::create(
            importType: ImportType::EtoroStatement,
            filePath: '/tmp/file.xlsx',
            originalFilename: 'statement.xlsx',
        );

        $importJob->markProcessing(2);
        $importJob->incrementProcessedChunks();
        $importJob->incrementProcessedChunks();

        self::assertSame(ImportStatus::Completed, $importJob->getStatus());
    }

    #[Test]
    public function itMarksFailedWhenErrorsExistAfterProcessing(): void
    {
        $importJob = ImportJob::create(
            importType: ImportType::EtoroStatement,
            filePath: '/tmp/file.xlsx',
            originalFilename: 'statement.xlsx',
        );

        $importJob->markProcessing(1);
        $importJob->addError(['sheet' => 'dividends', 'chunk' => 1, 'message' => 'Invalid row']);
        $importJob->incrementProcessedChunks();

        self::assertSame(ImportStatus::Failed, $importJob->getStatus());
    }
}
