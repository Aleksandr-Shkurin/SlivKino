#!/usr/bin/env sh
set -eu
SCRIPT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"
ROOT_DIR="$(CDPATH= cd -- "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT_DIR"
docker compose -f devops/compose.dev.yaml down
echo "SlivKino stopped. The development database volume was preserved."
