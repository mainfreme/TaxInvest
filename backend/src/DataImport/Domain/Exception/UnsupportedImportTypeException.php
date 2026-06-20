<?php

declare(strict_types=1);

namespace App\DataImport\Domain\Exception;

final class UnsupportedImportTypeException extends \InvalidArgumentException
{
    public static function forType(string $type): self
    {
        return new self(\sprintf('Unsupported import type "%s".', $type));
    }
}
