<?php

declare(strict_types=1);

namespace App\DataImport\Application\Service;

interface ImportFileReaderInterface
{
    /**
     * @return list<ImportSheetData>
     */
    public function read(string $filePath, string $importType): array;
}
