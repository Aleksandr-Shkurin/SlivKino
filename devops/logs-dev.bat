@echo off
setlocal
cd /d "%~dp0.."
docker compose -f devops/compose.yaml logs -f app
endlocal
