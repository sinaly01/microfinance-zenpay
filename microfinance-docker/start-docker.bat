@echo off
title ZEN-PAY Docker Stack
echo.
echo =====================================================
echo   ZEN-PAY - Lancement Docker
echo =====================================================
echo.
echo  [Oracle]   localhost:1521
echo  [API]      http://localhost:8080
echo  [Front]    http://localhost:5502
echo  [Swagger]  http://localhost:8080/swagger-ui/index.html
echo.

cd /d "%~dp0"

REM Verifie que Docker est lance
docker info >nul 2>&1
if errorlevel 1 (
    echo [ERREUR] Docker Desktop n'est pas demarre.
    echo          Lance Docker Desktop puis relance ce script.
    pause
    exit /b 1
)

REM Cree .env si absent
if not exist .env (
    echo Creation de .env depuis .env.example...
    copy .env.example .env >nul
)

echo Construction des images et demarrage...
docker compose up -d --build

if errorlevel 1 (
    echo.
    echo [ERREUR] Le demarrage a echoue. Voir les logs ci-dessus.
    pause
    exit /b 1
)

echo.
echo =====================================================
echo   Stack lancee. Initialisation Oracle ~2-5 min.
echo   Suivre l'init :  docker compose logs -f oracle
echo   Arreter      :  docker compose down
echo =====================================================
pause
