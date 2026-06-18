#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "==> PHP CS Fixer (check)"
"$ROOT/scripts/cs-check.sh"

echo "==> PHPStan"
"$ROOT/scripts/phpstan.sh"

echo "==> PHPUnit"
"$ROOT/scripts/phpunit.sh"

echo "==> Wszystkie kontrole QA zakończone pomyślnie."
