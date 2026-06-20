<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataImport\Infrastructure;

use App\DataImport\Infrastructure\Reader\CsvFileReader;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CsvFileReaderTest extends TestCase
{
    #[Test]
    public function itReadsCsvFixture(): void
    {
        $reader = new CsvFileReader();
        $fixturePath = \dirname(__DIR__, 3).'/Fixtures/DataImport/account_activity.csv';

        $sheet = $reader->read($fixturePath);

        self::assertSame('account_activity', $sheet->sheet);
        self::assertSame(['Date', 'Rodzaj', 'Szczegóły', 'Kwota', 'Jednostki'], $sheet->headers);
        self::assertCount(5, $sheet->rows);
        self::assertSame('Deposit', $sheet->rows[0][1]);
    }
}
