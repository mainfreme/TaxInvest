<?php

declare(strict_types=1);

namespace App\DataImport\Domain\Repository;

use App\DataImport\Domain\Model\Etoro\EtoroDividend;

interface EtoroDividendRepositoryInterface
{
    /**
     * @param list<EtoroDividend> $dividends
     */
    public function saveBatch(array $dividends): void;
}
