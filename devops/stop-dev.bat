@echo off
setlocal
cd /d "%~dp0.."
echo Stopping SlivKino development environment...
docker compose -f devops/compose.dev.yaml down
if errorlevel 1 (
  echo.
  echo Failed to stop. Make sure Docker Desktop is running.
  pause
  exit /b 1
)
echo SlivKino stopped. The development database volume was preserved.
pause
endlocal
