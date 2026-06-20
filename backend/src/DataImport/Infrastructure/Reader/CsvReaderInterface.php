<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Reader;

use App\DataImport\Application\Service\ImportSheetData;

interface CsvReaderInterface
{
    public function read(string $filePath): ImportSheetData;
}
