@echo off
chcp 65001 >nul
title Sistema PDV - Vite
cd /d "%~dp0"

if not exist node_modules (
    echo Execute iniciar.bat primeiro ou rode: npm install
    pause
    exit /b 1
)

echo Iniciando Vite (assets ao vivo)...
echo http://localhost:5173
echo.
npm run dev
pause
