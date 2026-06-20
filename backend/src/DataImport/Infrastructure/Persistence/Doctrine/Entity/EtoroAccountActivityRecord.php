<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Persistence\Doctrine\Entity;

use App\DataImport\Infrastructure\Persistence\Doctrine\Repository\EtoroAccountActivityRecordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EtoroAccountActivityRecordRepository::class)]
#[ORM\Table(name: 'etoro_account_activities')]
#[ORM\UniqueConstraint(name: 'uniq_etoro_account_activities_job_hash', columns: ['import_job_id', 'row_hash'])]
class EtoroAccountActivityRecord
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(name: 'import_job_id', type: UuidType::NAME)]
    private Uuid $importJobId;

    #[ORM\Column(name: 'row_hash', length: 64)]
    private string $rowHash;

    #[ORM\Column(name: 'row_number')]
    private int $rowNumber;

    /** @var array<string, string|null> */
    #[ORM\Column(type: Types::JSON)]
    private array $data;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getImportJobId(): Uuid
    {
        return $this->importJobId;
    }

    public function setImportJobId(Uuid $importJobId): void
    {
        $this->importJobId = $importJobId;
    }

    public function getRowHash(): string
    {
        return $this->rowHash;
    }

    public function setRowHash(string $rowHash): void
    {
        $this->rowHash = $rowHash;
    }

    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }

    public function setRowNumber(int $rowNumber): void
    {
        $this->rowNumber = $rowNumber;
    }

    /**
     * @return array<string, string|null>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, string|null> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
