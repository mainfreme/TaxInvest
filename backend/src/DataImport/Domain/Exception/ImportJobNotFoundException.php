<?php

declare(strict_types=1);

namespace App\DataImport\Domain\Exception;

use App\DataImport\Domain\ValueObject\ImportJobId;

final class ImportJobNotFoundException extends \RuntimeException
{
    public static function withId(ImportJobId $id): self
    {
        return new self(\sprintf('Import job with id "%s" was not found.', $id->toString()));
    }
}
