@echo off
setlocal
cd /d "%~dp0.."
docker compose -f devops/compose.yaml down
if errorlevel 1 (
    echo ERROR: Could not stop the SlivKino test stack.
    pause
    exit /b 1
)
echo SlivKino test stopped. Database and uploaded posters were preserved.
endlocal
