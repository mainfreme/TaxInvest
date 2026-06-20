<?php

declare(strict_types=1);

namespace App\DataImport\Application\Mapper;

use App\DataImport\Domain\Model\Etoro\EtoroAccountActivity;
use App\DataImport\Domain\Model\Etoro\EtoroClosedPosition;
use App\DataImport\Domain\Model\Etoro\EtoroDividend;
use App\DataImport\Domain\ValueObject\ImportJobId;

final class EtoroRowMapper
{
    /**
     * @param list<string> $headers
     * @param list<list<string|null>> $rows
     *
     * @return list<EtoroClosedPosition>
     */
    public function mapClosedPositions(ImportJobId $importJobId, array $headers, array $rows, int $startRowNumber): array
    {
        return $this->mapRows(
            $importJobId,
            $headers,
            $rows,
            $startRowNumber,
            fn (ImportJobId $jobId, string $hash, int $rowNumber, array $data): EtoroClosedPosition => EtoroClosedPosition::create($jobId, $hash, $rowNumber, $data),
        );
    }

    /**
     * @param list<string> $headers
     * @param list<list<string|null>> $rows
     *
     * @return list<EtoroAccountActivity>
     */
    public function mapAccountActivities(ImportJobId $importJobId, array $headers, array $rows, int $startRowNumber): array
    {
        return $this->mapRows(
            $importJobId,
            $headers,
            $rows,
            $startRowNumber,
            fn (ImportJobId $jobId, string $hash, int $rowNumber, array $data): EtoroAccountActivity => EtoroAccountActivity::create($jobId, $hash, $rowNumber, $data),
        );
    }

    /**
     * @param list<string> $headers
     * @param list<list<string|null>> $rows
     *
     * @return list<EtoroDividend>
     */
    public function mapDividends(ImportJobId $importJobId, array $headers, array $rows, int $startRowNumber): array
    {
        return $this->mapRows(
            $importJobId,
            $headers,
            $rows,
            $startRowNumber,
            fn (ImportJobId $jobId, string $hash, int $rowNumber, array $data): EtoroDividend => EtoroDividend::create($jobId, $hash, $rowNumber, $data),
        );
    }

    /**
     * @param list<string> $headers
     * @param list<list<string|null>> $rows
     * @param callable(ImportJobId, string, int, array<string, string|null>): T $factory
     *
     * @return list<T>
     *
     * @template T
     */
    private function mapRows(
        ImportJobId $importJobId,
        array $headers,
        array $rows,
        int $startRowNumber,
        callable $factory,
    ): array {
        $mapped = [];

        foreach ($rows as $index => $row) {
            $data = $this->combineRow($headers, $row);
            $rowNumber = $startRowNumber + $index;
            $hash = \hash('sha256', \json_encode($data, \JSON_THROW_ON_ERROR));
            $mapped[] = $factory($importJobId, $hash, $rowNumber, $data);
        }

        return $mapped;
    }

    /**
     * @param list<string> $headers
     * @param list<string|null> $row
     *
     * @return array<string, string|null>
     */
    public function combineRow(array $headers, array $row): array
    {
        $data = [];

        foreach ($headers as $index => $header) {
            $data[$header] = $row[$index] ?? null;
        }

        return $data;
    }
}
