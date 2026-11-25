#!/usr/bin/env sh
set -eu
cd "$(dirname "$0")/.."
[ -f .env ] || cp .env.example .env
docker compose -f devops/compose.yaml up -d
printf '\nSlivKino test is starting: http://localhost:3001\n'
