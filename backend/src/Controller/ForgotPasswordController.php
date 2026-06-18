<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ForgotPasswordRequestDto;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'api_forgot_password', methods: ['POST'])]
    public function forgotPassword(
        #[MapRequestPayload] ForgotPasswordRequestDto $request,
        UserRepository $userRepository,
    ): JsonResponse {
        $user = $userRepository->findOneBy(['email' => $request->email]);

        if ($user === null) {
            return $this->json([
                'message' => 'Jeśli podany adres e-mail jest zarejestrowany, wyślemy link do resetu hasła.',
            ]);
        }

        // TODO: wysłanie e-maila z linkiem resetu hasła (Messenger + mailer)

        return $this->json([
            'message' => 'Jeśli podany adres e-mail jest zarejestrowany, wyślemy link do resetu hasła.',
        ], Response::HTTP_ACCEPTED);
    }
}
