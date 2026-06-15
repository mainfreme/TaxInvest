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

## Endpointy testowe (backend)

- `http://localhost:8080/api/health` — status API
- `http://localhost:8080/api/db-check` — połączenie z PostgreSQL
- `http://localhost:8080/api/rabbitmq-check` — połączenie z RabbitMQ

## Frontendy

- Vue: `http://localhost:5173`
- Next.js: `http://localhost:3000`

## RabbitMQ Management

- URL: `http://localhost:15672`
- Login: `taxinvest` (domyślnie)
- Hasło: `secret` (domyślnie)

## Przydatne komendy

```bash
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
├── backend/           # PHP 8.4 API
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
