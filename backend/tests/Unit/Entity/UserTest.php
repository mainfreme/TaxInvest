<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    #[Test]
    public function itAlwaysIncludesRoleUser(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        self::assertContains('ROLE_USER', $user->getRoles());
    }

    #[Test]
    public function itUsesEmailAsUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        self::assertSame('test@example.com', $user->getUserIdentifier());
    }

    #[Test]
    public function itGeneratesUuidOnConstruction(): void
    {
        $user = new User();

        self::assertNotEmpty($user->getId()->toRfc4122());
    }
}
