@echo off
setlocal
cd /d "%~dp0.."
echo Starting SlivKino development environment...
docker compose -f devops/compose.dev.yaml up -d
if errorlevel 1 (
  echo.
  echo Failed to start. Make sure Docker Desktop is running.
  pause
  exit /b 1
)
echo.
echo SlivKino is starting at http://localhost:3000/
echo Admin panel: http://localhost:3000/admin.html
start "" http://localhost:3000/
endlocal
