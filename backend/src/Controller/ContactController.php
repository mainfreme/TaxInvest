<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ContactRequestDto;
use App\Message\AsyncExampleMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'api_contact', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function contact(
        #[MapRequestPayload] ContactRequestDto $request,
        MessageBusInterface $messageBus,
    ): JsonResponse {
        $messageBus->dispatch(new AsyncExampleMessage(
            email: $request->email,
            message: $request->message,
        ));

        return $this->json([
            'status' => 'queued',
            'message' => 'Wiadomość została dodana do kolejki asynchronicznej.',
        ], Response::HTTP_ACCEPTED);
    }
}
