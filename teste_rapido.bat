@echo off
echo ========================================
echo  TESTE RAPIDO DE CONECTIVIDADE
echo ========================================
echo.

echo 1. Testando PING...
ping -n 2 10.1.8.7
echo.

echo 2. Testando porta 3306 (TCP)...
powershell -Command "Test-NetConnection -ComputerName 10.1.8.7 -Port 3306 -WarningAction SilentlyContinue"
echo.

echo ========================================
echo RESULTADO:
echo ========================================
echo.
echo Se a porta mostrar "TcpTestSucceeded : True":
echo   ^> Problema e no Laravel/PHP - vamos corrigir
echo.
echo Se a porta mostrar "TcpTestSucceeded : False":
echo   ^> Servidor nao acessivel - precisamos alternativa
echo.
pause
