@echo off
setlocal
chcp 65001 >nul
title Sistema PDV
cd /d "%~dp0"

if exist "%ProgramFiles%\nodejs\node.exe" set "PATH=%ProgramFiles%\nodejs;%PATH%"
if exist "%LocalAppData%\Programs\nodejs\node.exe" set "PATH=%LocalAppData%\Programs\nodejs;%PATH%"

echo Sistema PDV - http://127.0.0.1:8000
echo Banco: MySQL remoto ^(.env^)
echo.

where php >nul 2>nul
if not errorlevel 1 goto havephp
echo ERRO: PHP nao encontrado. Instale PHP e adicione ao PATH.
pause
exit /b 1

:havephp
echo OK - PHP encontrado.

if exist ".env" goto haveenv
echo Criando .env...
copy /Y ".env.example" ".env" >nul
php artisan key:generate --no-interaction

:haveenv
echo OK - .env existe.

if exist "vendor\autoload.php" goto havevendor
echo.
echo Instalando Composer...
where composer >nul 2>nul
if not errorlevel 1 goto runcomposer
echo ERRO: Composer nao encontrado no PATH.
pause
exit /b 1

:runcomposer
composer install --no-interaction
if not errorlevel 1 goto havevendor
echo ERRO: composer install falhou.
pause
exit /b 1

:havevendor
echo OK - Composer / vendor OK.

if exist "node_modules" goto havenode
echo.
echo Instalando npm...
where npm >nul 2>nul
if not errorlevel 1 goto runnpm
echo ERRO: npm nao encontrado. Instale Node.js.
pause
exit /b 1

:runnpm
call npm install
if not errorlevel 1 goto havenode
echo ERRO: npm install falhou.
pause
exit /b 1

:havenode
echo OK - npm / node_modules OK.

if exist "public\build\manifest.json" goto havebuild
echo.
echo npm run build...
call npm run build
if not errorlevel 1 goto havebuild
echo ERRO: npm run build falhou.
pause
exit /b 1

:havebuild
echo OK - public\build OK.

echo.
echo Testando MySQL remoto...
php artisan config:clear >nul 2>&1
php artisan db:show >nul 2>&1
if errorlevel 1 goto dbfail

echo OK - MySQL remoto conectado.
echo.
echo Migrando ^(se houver pendencias^)...
php artisan migrate --no-interaction --force
goto serve

:dbfail
echo.
echo ERRO: nao foi possivel conectar ao MySQL remoto.
echo Execute testar-banco.bat para ver o detalhe.
echo.
echo Libere o IP do seu PC em MySQL remoto no painel da hospedagem.
echo Senha com caracteres especiais: use aspas simples no .env
echo   DB_PASSWORD='sua_senha'
echo.
pause
exit /b 1

:serve
echo.
echo ----------------------------------------------
echo  Login: use o usuario cadastrado no servidor
echo  ^(ex.: admin@sistema.pdv se existir no remoto^)
echo ----------------------------------------------
echo.
echo Servidor: http://127.0.0.1:8000
echo.
php artisan serve
pause
