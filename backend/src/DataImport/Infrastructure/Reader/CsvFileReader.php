<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Reader;

use App\DataImport\Application\Service\ImportSheetData;

final class CsvFileReader implements CsvReaderInterface
{
    public function read(string $filePath): ImportSheetData
    {
        $handle = \fopen($filePath, 'rb');

        if ($handle === false) {
            throw new \RuntimeException(\sprintf('Unable to open CSV file "%s".', $filePath));
        }

        $headerRow = \fgetcsv($handle, length: 0, separator: ',', enclosure: '"', escape: '\\');

        if ($headerRow === false) {
            \fclose($handle);

            throw new \RuntimeException('CSV file is empty.');
        }

        $headers = \array_map(static fn (mixed $header): string => \trim((string) $header), $headerRow);
        $rows = [];

        while (($row = \fgetcsv($handle, length: 0, separator: ',', enclosure: '"', escape: '\\')) !== false) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $normalized = [];

            for ($index = 0; $index < \count($headers); ++$index) {
                $value = $row[$index] ?? null;
                $normalized[] = $value === null || $value === '' ? null : (string) $value;
            }

            $rows[] = $normalized;
        }

        \fclose($handle);

        return new ImportSheetData(
            sheet: 'account_activity',
            headers: $headers,
            rows: $rows,
        );
    }

    /**
     * @param list<string|null> $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }
}
