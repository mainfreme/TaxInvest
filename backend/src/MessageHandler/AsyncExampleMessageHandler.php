<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\AsyncExampleMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AsyncExampleMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(AsyncExampleMessage $message): void
    {
        $this->logger->info('Async message processed.', [
            'email' => $message->email,
            'message' => $message->message,
        ]);
    }
}
