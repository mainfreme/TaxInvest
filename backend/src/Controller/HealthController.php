<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class HealthController extends AbstractController
{
    #[Route('/health', name: 'api_health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'service' => 'TaxInvest API',
            'php' => PHP_VERSION,
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }

    #[Route('/db-check', name: 'api_db_check', methods: ['GET'])]
    public function dbCheck(Connection $connection): JsonResponse
    {
        try {
            $version = $connection->fetchOne('SELECT version()');

            return $this->json([
                'connected' => true,
                'version' => \is_string($version) ? $version : null,
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'connected' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    #[Route('/rabbitmq-check', name: 'api_rabbitmq_check', methods: ['GET'])]
    public function rabbitMqCheck(): JsonResponse
    {
        if (!\extension_loaded('amqp')) {
            return $this->json([
                'connected' => false,
                'message' => 'AMQP extension not loaded',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        try {
            $connection = new \AMQPConnection([
                'host' => \getenv('RABBITMQ_HOST') ?: 'rabbitmq',
                'port' => (int) (\getenv('RABBITMQ_PORT') ?: 5672),
                'login' => \getenv('RABBITMQ_USER') ?: 'taxinvest',
                'password' => \getenv('RABBITMQ_PASSWORD') ?: 'secret',
                'vhost' => '/',
            ]);
            $connection->connect();
            $connected = $connection->isConnected();
            $connection->disconnect();

            return $this->json(['connected' => $connected]);
        } catch (\Throwable $e) {
            return $this->json([
                'connected' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
