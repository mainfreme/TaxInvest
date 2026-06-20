<?php

declare(strict_types=1);

namespace App\DataImport\Domain\Repository;

use App\DataImport\Domain\Model\Etoro\EtoroClosedPosition;

interface EtoroClosedPositionRepositoryInterface
{
    /**
     * @param list<EtoroClosedPosition> $positions
     */
    public function saveBatch(array $positions): void;
}
