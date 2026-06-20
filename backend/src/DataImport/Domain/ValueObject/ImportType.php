<?php

declare(strict_types=1);

namespace App\DataImport\Domain\ValueObject;

enum ImportType: string
{
    case EtoroStatement = 'etoro_statement';
}
