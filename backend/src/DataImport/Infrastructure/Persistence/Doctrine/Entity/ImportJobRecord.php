<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Persistence\Doctrine\Entity;

use App\DataImport\Infrastructure\Persistence\Doctrine\Repository\ImportJobRecordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ImportJobRecordRepository::class)]
#[ORM\Table(name: 'import_jobs')]
class ImportJobRecord
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(name: 'import_type', length: 50)]
    private string $importType;

    #[ORM\Column(name: 'file_path', length: 512)]
    private string $filePath;

    #[ORM\Column(name: 'original_filename', length: 255)]
    private string $originalFilename;

    #[ORM\Column(length: 20)]
    private string $status;

    #[ORM\Column(name: 'total_chunks')]
    private int $totalChunks = 0;

    #[ORM\Column(name: 'processed_chunks')]
    private int $processedChunks = 0;

    /** @var list<array{sheet: string, chunk: int, message: string}> */
    #[ORM\Column(type: Types::JSON)]
    private array $errors = [];

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getImportType(): string
    {
        return $this->importType;
    }

    public function setImportType(string $importType): void
    {
        $this->importType = $importType;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): void
    {
        $this->originalFilename = $originalFilename;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getTotalChunks(): int
    {
        return $this->totalChunks;
    }

    public function setTotalChunks(int $totalChunks): void
    {
        $this->totalChunks = $totalChunks;
    }

    public function getProcessedChunks(): int
    {
        return $this->processedChunks;
    }

    public function setProcessedChunks(int $processedChunks): void
    {
        $this->processedChunks = $processedChunks;
    }

    /**
     * @return list<array{sheet: string, chunk: int, message: string}>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param list<array{sheet: string, chunk: int, message: string}> $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
