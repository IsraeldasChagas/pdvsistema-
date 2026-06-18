@echo off
chcp 65001 >nul
title PDV — testar MySQL remoto
cd /d "%~dp0"

echo.
echo  Testando conexao com o MySQL remoto (.env)...
echo.

php artisan config:clear >nul 2>&1
php artisan db:show
if errorlevel 1 (
    echo.
    echo  ============================================
    echo  FALHA na conexao com o MySQL remoto
    echo  ============================================
    echo.
    echo  O login da aplicacao da erro 500 se o banco
    echo  nao conectar ^(sessao em database^).
    echo.
    echo  No painel da hospedagem ^(cPanel / Locaweb^):
    echo    1. MySQL remoto / Acesso remoto
    echo    2. Libere o IP do seu PC na internet
    echo       ^(o erro mostra algo como @181.220.x.x^)
    echo    3. Confirme usuario, senha e nome do banco
    echo.
    echo  Senha com $ no .env: use aspas simples
    echo    DB_PASSWORD='sua_senha'
    echo.
    pause
    exit /b 1
)

echo.
echo  Conexao OK.
pause
exit /b 0
