<?php

declare(strict_types=1);

namespace App\DataImport\Domain\Model;

use App\DataImport\Domain\ValueObject\ImportJobId;
use App\DataImport\Domain\ValueObject\ImportStatus;
use App\DataImport\Domain\ValueObject\ImportType;

final class ImportJob
{
    private ImportJobId $id;
    private ImportType $importType;
    private string $filePath;
    private string $originalFilename;
    private ImportStatus $status;
    private int $totalChunks;
    private int $processedChunks;
    /** @var list<array{sheet: string, chunk: int, message: string}> */
    private array $errors;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    /**
     * @param list<array{sheet: string, chunk: int, message: string}> $errors
     */
    private function __construct(
        ImportJobId $id,
        ImportType $importType,
        string $filePath,
        string $originalFilename,
        ImportStatus $status,
        int $totalChunks,
        int $processedChunks,
        array $errors,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->importType = $importType;
        $this->filePath = $filePath;
        $this->originalFilename = $originalFilename;
        $this->status = $status;
        $this->totalChunks = $totalChunks;
        $this->processedChunks = $processedChunks;
        $this->errors = $errors;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        ImportType $importType,
        string $filePath,
        string $originalFilename,
    ): self {
        $now = new \DateTimeImmutable();

        return new self(
            id: ImportJobId::generate(),
            importType: $importType,
            filePath: $filePath,
            originalFilename: $originalFilename,
            status: ImportStatus::Pending,
            totalChunks: 0,
            processedChunks: 0,
            errors: [],
            createdAt: $now,
            updatedAt: $now,
        );
    }

    /**
     * @param list<array{sheet: string, chunk: int, message: string}> $errors
     */
    public static function restore(
        ImportJobId $id,
        ImportType $importType,
        string $filePath,
        string $originalFilename,
        ImportStatus $status,
        int $totalChunks,
        int $processedChunks,
        array $errors,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: $id,
            importType: $importType,
            filePath: $filePath,
            originalFilename: $originalFilename,
            status: $status,
            totalChunks: $totalChunks,
            processedChunks: $processedChunks,
            errors: $errors,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function markProcessing(int $totalChunks): void
    {
        $this->status = $totalChunks === 0 ? ImportStatus::Completed : ImportStatus::Processing;
        $this->totalChunks = $totalChunks;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function incrementProcessedChunks(): void
    {
        ++$this->processedChunks;
        $this->updatedAt = new \DateTimeImmutable();

        if ($this->totalChunks > 0 && $this->processedChunks >= $this->totalChunks) {
            $this->status = $this->errors === [] ? ImportStatus::Completed : ImportStatus::Failed;
        }
    }

    /**
     * @param array{sheet: string, chunk: int, message: string} $error
     */
    public function addError(array $error): void
    {
        $this->errors[] = $error;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function markFailed(string $message): void
    {
        $this->status = ImportStatus::Failed;
        $this->errors[] = ['sheet' => 'orchestrator', 'chunk' => 0, 'message' => $message];
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function assignFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ImportJobId
    {
        return $this->id;
    }

    public function getImportType(): ImportType
    {
        return $this->importType;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function getStatus(): ImportStatus
    {
        return $this->status;
    }

    public function getTotalChunks(): int
    {
        return $this->totalChunks;
    }

    public function getProcessedChunks(): int
    {
        return $this->processedChunks;
    }

    /**
     * @return list<array{sheet: string, chunk: int, message: string}>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
