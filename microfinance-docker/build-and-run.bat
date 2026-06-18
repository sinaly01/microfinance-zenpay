@echo off
title ZEN-PAY — Build complet
setlocal

echo.
echo  =====================================================
echo   ZEN-PAY — Build et demarrage complet
echo  =====================================================
echo.

cd /d "%~dp0"

echo  [1/3] Construction des images...
echo  (php-front + spring-api — sans telechargement)
echo  -----------------------------------------------
docker compose build --pull=false
if %ERRORLEVEL% NEQ 0 (
  echo.
  echo  ERREUR : Le build a echoue. Voir les messages ci-dessus.
  pause
  exit /b 1
)

echo.
echo  [2/3] Demarrage des conteneurs...
echo  -----------------------------------------------
docker compose up -d
if %ERRORLEVEL% NEQ 0 (
  echo.
  echo  ERREUR : Impossible de demarrer les conteneurs.
  pause
  exit /b 1
)

echo.
echo  [3/3] Etat des conteneurs :
echo  -----------------------------------------------
docker compose ps

echo.
echo  =====================================================
echo   Application disponible sur :
echo   http://localhost:5502
echo  =====================================================
echo.
echo  Pour l'acces externe, lancer : lancer-tunnel.bat
echo.
pause
