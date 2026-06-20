<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\File;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class LocalImportFileStorage implements ImportFileStorageInterface
{
    public function __construct(
        private string $storageDir,
        private Filesystem $filesystem = new Filesystem(),
    ) {
    }

    public function store(UploadedFile $file, string $importJobId): string
    {
        $directory = \sprintf('%s/%s', $this->storageDir, $importJobId);
        $this->filesystem->mkdir($directory);

        $extension = $file->getClientOriginalExtension();
        $targetPath = \sprintf('%s/original.%s', $directory, $extension);
        $file->move($directory, \sprintf('original.%s', $extension));

        return $targetPath;
    }
}
