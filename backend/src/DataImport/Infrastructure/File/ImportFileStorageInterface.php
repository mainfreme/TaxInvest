<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImportFileStorageInterface
{
    public function store(UploadedFile $file, string $importJobId): string;
}
