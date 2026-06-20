<?php

declare(strict_types=1);

namespace App\DataImport\Application\Message;

final readonly class StartImportMessage
{
    public function __construct(
        public string $importJobId,
    ) {
    }
}
