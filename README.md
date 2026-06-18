# TaxInvest

Stack aplikacji TaxInvest w Dockerze.

## Serwisy

| Serwis | Port | Opis |
|--------|------|------|
| **nginx + PHP 8.4** | 8080 | Backend API |
| **PostgreSQL 16** | 5432 | Baza danych |
| **RabbitMQ** | 5672 | Kolejka wiadomości |
| **RabbitMQ Management** | 15672 | Panel administracyjny |
| **Vue (Vite)** | 5173 | Frontend Vue 3 |
| **Next.js** | 3000 | Frontend React (Next.js) |

## Wymagania

- Docker Desktop / Docker Engine 24+
- Docker Compose v2

## Uruchomienie

```bash
# 1. Skopiuj zmienne środowiskowe
cp .env.example .env

# 2. Uruchom wszystkie kontenery
docker compose up -d --build

# 3. Sprawdź status
docker compose ps
```

## Endpointy testowe (backend Symfony)

- `GET http://localhost:8080/api/health` — status API
- `GET http://localhost:8080/api/db-check` — połączenie z PostgreSQL
- `GET http://localhost:8080/api/rabbitmq-check` — połączenie z RabbitMQ
- `POST http://localhost:8080/api/login` — logowanie JWT (`email`, `password`)
- `POST http://localhost:8080/api/forgot-password` — przypomnienie hasła (`email`)
- `POST http://localhost:8080/api/contact` — przykład REST + walidacja + kolejka async (wymaga JWT)
- `GET http://localhost:8080/api/docs` — dokumentacja OpenAPI (Swagger UI)
- `GET http://localhost:8080/api/users` — lista użytkowników (API Platform, wymaga JWT)
- `POST http://localhost:8080/api/users` — rejestracja użytkownika (API Platform, publiczny)

### Inicjalizacja backendu (pierwsze uruchomienie)

```bash
docker compose up -d --build
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console lexik:jwt:generate-keypair --skip-if-exists
docker compose exec php php bin/console app:create-user --email=admin@taxinvest.local --password=secret
docker compose exec php php bin/console messenger:setup-transports
docker compose exec -d php php bin/console messenger:consume async -vv
```

Logowanie JWT:

```bash
curl -s -X POST http://localhost:8080/api/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"admin@taxinvest.local","password":"secret"}'
```

## Frontendy

- Vue: `http://localhost:5173`
- Next.js: `http://localhost:3000`

## RabbitMQ Management

- URL: `http://localhost:15672`
- Login: `taxinvest` (domyślnie)
- Hasło: `secret` (domyślnie)

## Przydatne komendy

```bash
# QA backend (Composer)
cd backend && composer qa          # cs-check + phpstan + phpunit
cd backend && composer cs-fix      # napraw styl kodu
cd backend && composer cs-check    # sprawdź styl kodu
cd backend && composer phpstan     # analiza statyczna
cd backend && composer test        # testy PHPUnit

# QA backend (skrypty shell)
cd backend && ./scripts/qa.sh
cd backend && ./scripts/cs-fix.sh
cd backend && ./scripts/cs-check.sh
cd backend && ./scripts/phpstan.sh
cd backend && ./scripts/phpunit.sh

# W Dockerze
docker compose exec php composer qa

# Logi wszystkich serwisów
docker compose logs -f

# Logi konkretnego serwisu
docker compose logs -f php

# Zatrzymanie
docker compose down

# Zatrzymanie + usunięcie wolumenów (baza, kolejka)
docker compose down -v

# Wejście do kontenera PHP
docker compose exec php sh

# Wejście do PostgreSQL
docker compose exec postgres psql -U taxinvest -d taxinvest
```

## Struktura projektu

```
├── backend/           # Symfony 8 API (API Platform, JWT, Messenger, Validator)
├── frontend-vue/      # Vue 3 + Vite
├── frontend-next/     # Next.js 15 (React)
├── docker/
│   ├── php/           # Dockerfile PHP + rozszerzenia (pgsql, amqp)
│   └── nginx/         # Konfiguracja Nginx
├── docker-compose.yml
└── .env.example
```

## Uwaga o frontendach

Projekt zawiera **dwa osobne frontendy**:

- **Vue 3** — framework JavaScript oparty o Vue
- **Next.js** — framework React (nie Vue)

Jeśli chciałeś użyć Vue z SSR, rozważ **Nuxt.js** zamiast Next.js.
