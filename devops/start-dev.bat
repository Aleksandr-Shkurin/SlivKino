@echo off
setlocal
cd /d "%~dp0.."

echo [SlivKino test] Preparing local environment...
if not exist .env (
    copy .env.example .env >nul
    if errorlevel 1 (
        echo ERROR: Could not create .env from .env.example
        pause
        exit /b 1
    )
)

echo [SlivKino test] Starting Docker Compose on http://localhost:3001 ...
docker compose -f devops/compose.yaml up -d
if errorlevel 1 (
    echo ERROR: Docker Compose failed. Check Docker Desktop and logs.
    pause
    exit /b 1
)

start "" http://localhost:3001/
echo Site is starting. First launch may take a few minutes while Docker builds the image.
endlocal
