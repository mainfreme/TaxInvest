<?php

declare(strict_types=1);

namespace App\Message;

final readonly class AsyncExampleMessage
{
    public function __construct(
        public string $email,
        public string $message,
    ) {
    }
}
