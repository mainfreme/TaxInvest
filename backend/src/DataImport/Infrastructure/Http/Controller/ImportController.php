<?php

declare(strict_types=1);

namespace App\DataImport\Infrastructure\Http\Controller;

use App\DataImport\Application\Dto\ImportJobView;
use App\DataImport\Application\Message\StartImportMessage;
use App\DataImport\Domain\Model\ImportJob;
use App\DataImport\Domain\Repository\ImportJobRepositoryInterface;
use App\DataImport\Domain\ValueObject\ImportJobId;
use App\DataImport\Domain\ValueObject\ImportType;
use App\DataImport\Infrastructure\File\ImportFileStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/imports')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ImportController extends AbstractController
{
    private const ALLOWED_EXTENSIONS = ['csv', 'xls', 'xlsx'];

    public function __construct(
        private ImportJobRepositoryInterface $importJobRepository,
        private ImportFileStorageInterface $fileStorage,
        private MessageBusInterface $messageBus,
        private int $maxFileSize,
    ) {
    }

    #[Route('', name: 'api_imports_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            throw new BadRequestHttpException('Missing file upload.');
        }

        if ($file->getSize() > $this->maxFileSize) {
            throw new BadRequestHttpException('File is too large.');
        }

        $extension = \strtolower((string) $file->getClientOriginalExtension());

        if (!\in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new BadRequestHttpException('Unsupported file type. Allowed: csv, xls, xlsx.');
        }

        $importTypeValue = (string) $request->request->get('importType', ImportType::EtoroStatement->value);

        try {
            $importType = ImportType::from($importTypeValue);
        } catch (\ValueError) {
            throw new BadRequestHttpException(\sprintf('Unsupported import type "%s".', $importTypeValue));
        }

        $importJob = ImportJob::create(
            importType: $importType,
            filePath: '',
            originalFilename: (string) $file->getClientOriginalName(),
        );

        $storedPath = $this->fileStorage->store($file, $importJob->getId()->toString());
        $importJob->assignFilePath($storedPath);

        $this->importJobRepository->save($importJob);
        $this->messageBus->dispatch(new StartImportMessage($importJob->getId()->toString()));

        return $this->json([
            'importJobId' => $importJob->getId()->toString(),
            'status' => 'queued',
        ], Response::HTTP_ACCEPTED);
    }

    #[Route('/{id}', name: 'api_imports_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $importJobId = ImportJobId::fromString($id);
        $importJob = $this->importJobRepository->findById($importJobId);

        if ($importJob === null) {
            throw new NotFoundHttpException(\sprintf('Import job with id "%s" was not found.', $id));
        }

        return $this->json(ImportJobView::fromDomain($importJob));
    }
}
