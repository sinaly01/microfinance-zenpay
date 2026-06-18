# Tue le processus sur le port 8080 et relance Spring Boot
$pid8080 = (netstat -ano | findstr :8080 | Select-String "LISTENING" | ForEach-Object { ($_ -split "\s+")[-1] } | Select-Object -First 1)
if ($pid8080) {
    Write-Host "Arrêt du processus PID $pid8080 sur le port 8080..."
    taskkill /PID $pid8080 /F
    Start-Sleep -Seconds 2
}
Write-Host "Démarrage de Spring Boot..."
& "C:\Program Files\JetBrains\IntelliJ IDEA 2025.3.2\plugins\maven\lib\maven3\bin\mvn.cmd" spring-boot:run -f pom.xml
