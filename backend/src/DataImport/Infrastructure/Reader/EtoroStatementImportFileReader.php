<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Reader;

use App\DataImport\Application\Service\ImportFileReaderInterface;
use App\DataImport\Application\Service\ImportSheetData;
use App\DataImport\Domain\Exception\UnsupportedImportTypeException;
use App\DataImport\Domain\ValueObject\EtoroSheet;
use App\DataImport\Domain\ValueObject\ImportType;

final class EtoroStatementImportFileReader implements ImportFileReaderInterface
{
    public function __construct(
        private PhpSpreadsheetFileReader $spreadsheetReader,
        private CsvFileReader $csvReader,
    ) {
    }

    public function read(string $filePath, string $importType): array
    {
        if ($importType !== ImportType::EtoroStatement->value) {
            throw UnsupportedImportTypeException::forType($importType);
        }

        $extension = \strtolower((string) \pathinfo($filePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'csv' => [$this->csvReader->read($filePath)],
            'xls', 'xlsx' => $this->spreadsheetReader->readSheets(
                $filePath,
                \array_map(static fn (EtoroSheet $sheet): string => $sheet->label(), EtoroSheet::all()),
            ),
            default => throw new \InvalidArgumentException(\sprintf('Unsupported file extension "%s".', $extension)),
        };
    }
}
