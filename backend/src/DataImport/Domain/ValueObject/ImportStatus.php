<?php

declare(strict_types=1);

namespace App\DataImport\Domain\ValueObject;

enum ImportStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
}
