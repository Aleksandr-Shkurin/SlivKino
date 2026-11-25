#!/usr/bin/env sh
set -eu
cd "$(dirname "$0")/.."
[ -f .env ] || cp .env.example .env
docker compose -f devops/compose.yaml up -d --build
printf '\nSlivKino test rebuilt: http://localhost:3001\n'
