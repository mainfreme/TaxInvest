<?php

declare(strict_types=1);

namespace App\DataImport\Application\Service;

final class ImportChunkSplitter
{
    public function __construct(
        private int $chunkSize = 50,
    ) {
    }

    /**
     * @param list<list<string|null>> $rows
     *
     * @return list<list<list<string|null>>>
     */
    public function split(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        return \array_chunk($rows, $this->chunkSize);
    }

    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }
}
