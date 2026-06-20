<?php

declare(strict_types=1);

namespace App\DataImport\Application\Service;

final readonly class ImportSheetData
{
    /**
     * @param list<string> $headers
     * @param list<list<string|null>> $rows
     */
    public function __construct(
        public string $sheet,
        public array $headers,
        public array $rows,
    ) {
    }
}
