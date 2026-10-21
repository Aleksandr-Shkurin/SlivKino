#!/usr/bin/env sh
set -eu
SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"
ROOT_DIR="$(CDPATH= cd -- "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT_DIR"
docker compose -f devops/compose.dev.yaml up --build -d
echo "SlivKino is starting: http://localhost:3000/"
echo "Admin panel: http://localhost:3000/admin.html"
