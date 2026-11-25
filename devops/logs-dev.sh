#!/usr/bin/env sh
set -eu
cd "$(dirname "$0")/.."
docker compose -f devops/compose.yaml logs -f app
