<?php

declare(strict_types=1);

namespace App\Tests\Integration\DataImport;

use App\DataImport\Application\Handler\ProcessImportChunkHandler;
use App\DataImport\Application\Mapper\EtoroRowMapper;
use App\DataImport\Application\Message\ProcessImportChunkMessage;
use App\DataImport\Domain\Model\ImportJob;
use App\DataImport\Domain\Repository\EtoroAccountActivityRepositoryInterface;
use App\DataImport\Domain\Repository\EtoroClosedPositionRepositoryInterface;
use App\DataImport\Domain\Repository\EtoroDividendRepositoryInterface;
use App\DataImport\Domain\Repository\ImportJobRepositoryInterface;
use App\DataImport\Domain\ValueObject\ImportStatus;
use App\DataImport\Domain\ValueObject\ImportType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class ProcessImportChunkHandlerTest extends TestCase
{
    #[Test]
    public function itProcessesAccountActivityChunkAndUpdatesJobProgress(): void
    {
        $importJob = ImportJob::create(
            importType: ImportType::EtoroStatement,
            filePath: '/tmp/file.csv',
            originalFilename: 'file.csv',
        );
        $importJob->markProcessing(1);

        $importJobRepository = new InMemoryImportJobRepository($importJob);
        $activityRepository = new InMemoryEtoroAccountActivityRepository();

        $handler = new ProcessImportChunkHandler(
            importJobRepository: $importJobRepository,
            closedPositionRepository: new InMemoryEtoroClosedPositionRepository(),
            accountActivityRepository: $activityRepository,
            dividendRepository: new InMemoryEtoroDividendRepository(),
            rowMapper: new EtoroRowMapper(),
            logger: new NullLogger(),
        );

        $handler(new ProcessImportChunkMessage(
            importJobId: $importJob->getId()->toString(),
            sheet: 'account_activity',
            chunkNumber: 1,
            totalChunks: 1,
            startRowNumber: 1,
            headers: ['Data', 'Rodzaj'],
            rows: [['2023-01-01', 'Deposit']],
        ));

        self::assertSame(ImportStatus::Completed, $importJob->getStatus());
        self::assertCount(1, $activityRepository->saved);
    }
}

final class InMemoryImportJobRepository implements ImportJobRepositoryInterface
{
    public function __construct(
        private ImportJob $importJob,
    ) {
    }

    public function save(ImportJob $importJob): void
    {
        $this->importJob = $importJob;
    }

    public function findById(\App\DataImport\Domain\ValueObject\ImportJobId $id): ?ImportJob
    {
        return $this->importJob->getId()->toString() === $id->toString() ? $this->importJob : null;
    }
}

final class InMemoryEtoroClosedPositionRepository implements EtoroClosedPositionRepositoryInterface
{
    /** @var list<\App\DataImport\Domain\Model\Etoro\EtoroClosedPosition> */
    public array $saved = [];

    public function saveBatch(array $positions): void
    {
        $this->saved = \array_merge($this->saved, $positions);
    }
}

final class InMemoryEtoroAccountActivityRepository implements EtoroAccountActivityRepositoryInterface
{
    /** @var list<\App\DataImport\Domain\Model\Etoro\EtoroAccountActivity> */
    public array $saved = [];

    public function saveBatch(array $activities): void
    {
        $this->saved = \array_merge($this->saved, $activities);
    }
}

final class InMemoryEtoroDividendRepository implements EtoroDividendRepositoryInterface
{
    /** @var list<\App\DataImport\Domain\Model\Etoro\EtoroDividend> */
    public array $saved = [];

    public function saveBatch(array $dividends): void
    {
        $this->saved = \array_merge($this->saved, $dividends);
    }
}
