#!/usr/bin/env sh
set -eu
cd "$(dirname "$0")/.."
docker compose -f devops/compose.yaml down
printf 'SlivKino test stopped. Database and uploaded posters were preserved.\n'
