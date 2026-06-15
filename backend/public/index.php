<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

match ($path) {
    '/api/health' => jsonResponse([
        'status' => 'ok',
        'service' => 'TaxInvest API',
        'php' => PHP_VERSION,
        'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
    ]),
    '/api/db-check' => jsonResponse(checkDatabase()),
    '/api/rabbitmq-check' => jsonResponse(checkRabbitMq()),
    default => jsonResponse(['error' => 'Not found'], 404),
};

function jsonResponse(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    exit;
}

function checkDatabase(): array
{
    $host = getenv('POSTGRES_HOST') ?: 'postgres';
    $port = getenv('POSTGRES_PORT') ?: '5432';
    $db = getenv('POSTGRES_DB') ?: 'taxinvest';
    $user = getenv('POSTGRES_USER') ?: 'taxinvest';
    $password = getenv('POSTGRES_PASSWORD') ?: 'secret';

    try {
        $pdo = new PDO(
            sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $db),
            $user,
            $password,
        );
        $version = $pdo->query('SELECT version()')->fetchColumn();

        return [
            'connected' => true,
            'version' => is_string($version) ? $version : null,
        ];
    } catch (Throwable $e) {
        return [
            'connected' => false,
            'message' => $e->getMessage(),
        ];
    }
}

function checkRabbitMq(): array
{
    if (!extension_loaded('amqp')) {
        return ['connected' => false, 'message' => 'AMQP extension not loaded'];
    }

    try {
        $connection = new AMQPConnection([
            'host' => getenv('RABBITMQ_HOST') ?: 'rabbitmq',
            'port' => (int) (getenv('RABBITMQ_PORT') ?: 5672),
            'login' => getenv('RABBITMQ_USER') ?: 'taxinvest',
            'password' => getenv('RABBITMQ_PASSWORD') ?: 'secret',
            'vhost' => '/',
        ]);
        $connection->connect();

        $connected = $connection->isConnected();
        $connection->disconnect();

        return ['connected' => $connected];
    } catch (Throwable $e) {
        return [
            'connected' => false,
            'message' => $e->getMessage(),
        ];
    }
}
