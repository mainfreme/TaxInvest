<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataImport\Application;

use App\DataImport\Application\Mapper\EtoroRowMapper;
use App\DataImport\Domain\ValueObject\ImportJobId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EtoroRowMapperTest extends TestCase
{
    #[Test]
    public function itMapsClosedPositionRows(): void
    {
        $mapper = new EtoroRowMapper();
        $importJobId = ImportJobId::fromString('019abc00-0000-7000-8000-000000000001');
        $headers = ['ID pozycji', 'Działanie'];
        $rows = [['123', 'Buy']];

        $mapped = $mapper->mapClosedPositions($importJobId, $headers, $rows, 1);

        self::assertCount(1, $mapped);
        self::assertSame('123', $mapped[0]->getData()['ID pozycji']);
        self::assertSame('Buy', $mapped[0]->getData()['Działanie']);
    }

    #[Test]
    public function itMapsAccountActivityRows(): void
    {
        $mapper = new EtoroRowMapper();
        $importJobId = ImportJobId::fromString('019abc00-0000-7000-8000-000000000001');
        $headers = ['Data', 'Rodzaj'];
        $rows = [['2023-01-01', 'Deposit']];

        $mapped = $mapper->mapAccountActivities($importJobId, $headers, $rows, 1);

        self::assertCount(1, $mapped);
        self::assertSame('2023-01-01', $mapped[0]->getData()['Data']);
    }

    #[Test]
    public function itMapsDividendRows(): void
    {
        $mapper = new EtoroRowMapper();
        $importJobId = ImportJobId::fromString('019abc00-0000-7000-8000-000000000001');
        $headers = ['Data płatności', 'Nazwa instrumentu'];
        $rows = [['2023-03-01', 'AAPL']];

        $mapped = $mapper->mapDividends($importJobId, $headers, $rows, 1);

        self::assertCount(1, $mapped);
        self::assertSame('AAPL', $mapped[0]->getData()['Nazwa instrumentu']);
    }

    #[Test]
    public function itCombinesHeadersWithRowValues(): void
    {
        $mapper = new EtoroRowMapper();

        $combined = $mapper->combineRow(['A', 'B'], ['1', null]);

        self::assertSame(['A' => '1', 'B' => null], $combined);
    }
}
