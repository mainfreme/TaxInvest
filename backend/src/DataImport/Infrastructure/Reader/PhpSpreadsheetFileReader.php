<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Reader;

use App\DataImport\Application\Service\ImportSheetData;
use PhpOffice\PhpSpreadsheet\IOFactory;

final class PhpSpreadsheetFileReader implements SpreadsheetReaderInterface
{
    public function readSheets(string $filePath, array $sheetNames): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheets = [];

        foreach ($sheetNames as $sheetName) {
            $worksheet = $spreadsheet->getSheetByName($sheetName);

            if ($worksheet === null) {
                continue;
            }

            $rows = $worksheet->toArray(null, true, true, false);

            if ($rows === []) {
                continue;
            }

            $headers = $this->normalizeHeaders(\array_shift($rows));
            $normalizedRows = [];

            foreach ($rows as $row) {
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $normalizedRows[] = $this->normalizeRow($row, \count($headers));
            }

            $sheets[] = new ImportSheetData(
                sheet: $this->resolveSheetKey($sheetName),
                headers: $headers,
                rows: $normalizedRows,
            );
        }

        return $sheets;
    }

    /**
     * @param list<mixed> $headers
     *
     * @return list<string>
     */
    private function normalizeHeaders(array $headers): array
    {
        return \array_map(
            static fn (mixed $header): string => \trim((string) $header),
            $headers,
        );
    }

    /**
     * @param list<mixed> $row
     *
     * @return list<string|null>
     */
    private function normalizeRow(array $row, int $headerCount): array
    {
        $normalized = [];

        for ($index = 0; $index < $headerCount; ++$index) {
            $value = $row[$index] ?? null;
            $normalized[] = $value === null || $value === '' ? null : (string) $value;
        }

        return $normalized;
    }

    /**
     * @param list<mixed> $row
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

    private function resolveSheetKey(string $sheetName): string
    {
        return match ($sheetName) {
            'Pozycje zamknięte' => 'closed_positions',
            'Aktywność na rachunku' => 'account_activity',
            'Dywidendy' => 'dividends',
            default => \strtolower(\str_replace(' ', '_', $sheetName)),
        };
    }
}
