<?php

declare(strict_types=1);

namespace App\DataImport\Domain\Repository;

use App\DataImport\Domain\Model\Etoro\EtoroAccountActivity;

interface EtoroAccountActivityRepositoryInterface
{
    /**
     * @param list<EtoroAccountActivity> $activities
     */
    public function saveBatch(array $activities): void;
}
