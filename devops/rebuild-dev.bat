@echo off
setlocal
cd /d "%~dp0.."
if not exist .env copy .env.example .env >nul
docker compose -f devops/compose.yaml up -d --build
if errorlevel 1 (
    echo ERROR: Rebuild failed. Check Docker output.
    pause
    exit /b 1
)
start "" http://localhost:3001/
endlocal
