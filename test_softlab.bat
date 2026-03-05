@echo off
REM Script para testar conexão com Softlab
REM Execute: test_softlab.bat

echo ===============================================================
echo  DIAGNOSTICO DE CONEXAO SOFTLAB
echo ===============================================================
echo.

REM Ler configurações do .env
for /f "tokens=1,2 delims==" %%a in ('findstr /B "SOFTLAB_DB_" .env') do (
    set %%a=%%b
)

echo Configuracoes encontradas no .env:
echo   Host: %SOFTLAB_DB_HOST%
echo   Porta: %SOFTLAB_DB_PORT%
echo   Database: %SOFTLAB_DB_DATABASE%
echo   Usuario: %SOFTLAB_DB_USERNAME%
echo.

REM Teste 1: Ping
echo [1/4] Testando PING ao servidor...
ping -n 2 %SOFTLAB_DB_HOST% > nul
if %errorlevel%==0 (
    echo   [OK] Host esta acessivel
) else (
    echo   [ERRO] Host nao responde ao ping
    echo   Isso pode ser normal se ICMP estiver bloqueado
)
echo.

REM Teste 2: Porta
echo [2/4] Testando porta %SOFTLAB_DB_PORT%...
powershell -Command "Test-NetConnection -ComputerName %SOFTLAB_DB_HOST% -Port %SOFTLAB_DB_PORT% -InformationLevel Quiet" > nul
if %errorlevel%==0 (
    echo   [OK] Porta %SOFTLAB_DB_PORT% esta acessivel
) else (
    echo   [ERRO] Porta %SOFTLAB_DB_PORT% nao esta acessivel
    echo.
    echo   POSSVEIS CAUSAS:
    echo   - MySQL nao esta rodando no servidor
    echo   - Firewall bloqueando a porta
    echo   - Porta incorreta no .env
    echo.
    echo   SOLUCOES:
    echo   1. No servidor, verifique se MySQL esta rodando
    echo   2. Abra a porta 3306 no firewall do servidor
    echo   3. Verifique se o MySQL esta escutando em 0.0.0.0
    echo.
    pause
    exit /b 1
)
echo.

REM Teste 3: Verificar se Python está instalado
echo [3/4] Verificando Python...
python --version >nul 2>&1
if %errorlevel%==0 (
    echo   [OK] Python encontrado
    
    REM Verificar se mysql-connector está instalado
    python -c "import mysql.connector" >nul 2>&1
    if %errorlevel%==0 (
        echo   [OK] mysql-connector-python instalado
    ) else (
        echo   [AVISO] mysql-connector-python nao instalado
        echo   Instalando...
        pip install mysql-connector-python
    )
    
    echo.
    echo [4/4] Executando teste completo de conexao MySQL...
    python test_softlab_connection.py
) else (
    echo   [AVISO] Python nao encontrado
    echo   Instale Python 3 para testes completos: https://www.python.org/
    echo.
    echo [4/4] Pulando teste MySQL (requer Python)
)

echo.
echo ===============================================================
echo  FIM DO DIAGNOSTICO
echo ===============================================================
echo.
echo Se todos os testes passaram mas ainda ha erro no Laravel:
echo   1. Execute: php artisan config:clear
echo   2. Verifique o .env e confirme os dados
echo   3. Tente conectar com HeidiSQL ou MySQL Workbench primeiro
echo.
pause
