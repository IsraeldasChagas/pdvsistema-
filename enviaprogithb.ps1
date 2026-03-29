# Envia o projeto para o GitHub: add, commit e push (branch main).
# Uso:
#   .\enviaprogithb.ps1
#   .\enviaprogithb.ps1 "mensagem do commit"

$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

$mensagem = $args[0]
if (-not $mensagem) {
    $mensagem = "Atualização $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
}

Write-Host ">> Diretório: $(Get-Location)" -ForegroundColor Cyan
Write-Host ">> git add -A" -ForegroundColor Gray
git add -A

$alteracoes = git status --porcelain
if (-not $alteracoes) {
    Write-Host ">> Nada alterado para commitar." -ForegroundColor Yellow
    Write-Host ">> git push origin main" -ForegroundColor Gray
    git push origin main
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    Write-Host ">> Concluído (só push)." -ForegroundColor Green
    exit 0
}

Write-Host ">> git commit -m `"$mensagem`"" -ForegroundColor Gray
git commit -m $mensagem
if ($LASTEXITCODE -ne 0) {
    Write-Host ">> Falha no commit." -ForegroundColor Red
    exit $LASTEXITCODE
}

Write-Host ">> git push origin main" -ForegroundColor Gray
git push origin main
if ($LASTEXITCODE -ne 0) {
    Write-Host ">> Falha no push." -ForegroundColor Red
    exit $LASTEXITCODE
}

Write-Host ">> Enviado para o GitHub com sucesso." -ForegroundColor Green
