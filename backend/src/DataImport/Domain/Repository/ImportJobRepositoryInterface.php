<?php

declare(strict_types=1);

namespace App\DataImport\Domain\Repository;

use App\DataImport\Domain\Model\ImportJob;
use App\DataImport\Domain\ValueObject\ImportJobId;

interface ImportJobRepositoryInterface
{
    public function save(ImportJob $importJob): void;

    public function findById(ImportJobId $id): ?ImportJob;
}
