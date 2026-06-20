<?php

declare(strict_types=1);

namespace App\DataImport\Application\Message;

final readonly class ProcessImportChunkMessage
{
    /**
     * @param list<string> $headers
     * @param list<list<string|null>> $rows
     */
    public function __construct(
        public string $importJobId,
        public string $sheet,
        public int $chunkNumber,
        public int $totalChunks,
        public int $startRowNumber,
        /** @var list<string> */
        public array $headers,
        /** @var list<list<string|null>> $rows */
        public array $rows,
    ) {
    }
}
