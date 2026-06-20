<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Reader;

use App\DataImport\Application\Service\ImportSheetData;

interface SpreadsheetReaderInterface
{
    /**
     * @param list<string> $sheetNames
     *
     * @return list<ImportSheetData>
     */
    public function readSheets(string $filePath, array $sheetNames): array;
}
