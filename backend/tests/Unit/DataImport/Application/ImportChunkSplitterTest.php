<?php

declare(strict_types=1);

namespace App\Tests\Unit\DataImport\Application;

use App\DataImport\Application\Service\ImportChunkSplitter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ImportChunkSplitterTest extends TestCase
{
    #[Test]
    #[DataProvider('splitProvider')]
    public function itSplitsRowsIntoChunks(int $rowCount, int $chunkSize, int $expectedChunks): void
    {
        $splitter = new ImportChunkSplitter($chunkSize);
        $rows = \array_map(static fn (): array => ['1'], \array_fill(0, $rowCount, null));

        self::assertCount($expectedChunks, $splitter->split($rows));
    }

    /**
     * @return iterable<string, array{int, int, int}>
     */
    public static function splitProvider(): iterable
    {
        yield 'empty rows' => [0, 50, 0];
        yield 'single row' => [1, 50, 1];
        yield 'exact chunk' => [50, 50, 1];
        yield 'chunk plus one' => [51, 50, 2];
        yield 'multiple chunks' => [125, 50, 3];
    }
}
