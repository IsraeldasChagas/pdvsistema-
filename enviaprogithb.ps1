# Envia o projeto para o GitHub: add, commit e push (branch main).
# Uso:
#   .\enviaprogithb.ps1
#   .\enviaprogithb.ps1 "mensagem do commit"

$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

function Show-ResumoEFechar {
    param(
        [int]$CodigoSaida = 0,
        [string]$MensagemExtra = ""
    )
    Write-Host ""
    Write-Host "======== Resumo ========" -ForegroundColor Cyan
    Write-Host "Pasta: $(Get-Location)"
    if ($MensagemExtra) {
        $cor = if ($CodigoSaida -ne 0) { "Red" } else { "DarkGray" }
        Write-Host $MensagemExtra -ForegroundColor $cor
    }
    $ultimo = git log -1 --oneline --decorate 2>$null
    if ($ultimo) {
        Write-Host "Último commit: $ultimo"
    }
    Write-Host ""
    Write-Host "Estado do repositório:" -ForegroundColor Gray
    git status -sb 2>$null
    Write-Host "========================" -ForegroundColor Cyan
    Write-Host ""
    $resp = Read-Host "Deseja fechar esta janela agora? (S = sim, N = não)"
    if ($resp -match '^[sS]') {
        exit $CodigoSaida
    }
    Write-Host ""
    Read-Host "Pressione Enter para fechar quando terminar"
    exit $CodigoSaida
}

$mensagem = $args[0]
if (-not $mensagem) {
    $mensagem = "Atualização $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
}

Write-Host ""
Write-Host ">> Diretório: $(Get-Location)" -ForegroundColor Cyan
Write-Host ">> git add -A" -ForegroundColor Gray
git add -A

$alteracoes = git status --porcelain
if (-not $alteracoes) {
    Write-Host ">> Nada novo para commitar (working tree limpo)." -ForegroundColor Yellow
    Write-Host ">> git push origin main" -ForegroundColor Gray
    git push origin main
    if ($LASTEXITCODE -ne 0) {
        Show-ResumoEFechar -CodigoSaida $LASTEXITCODE -MensagemExtra ">> Falha no push."
    }
    Write-Host ">> Push concluído." -ForegroundColor Green
    Show-ResumoEFechar -CodigoSaida 0 -MensagemExtra ">> Nenhum commit novo; apenas push."
}

Write-Host ">> Arquivos em staging / alterados:" -ForegroundColor Gray
git status -s

Write-Host ">> git commit -m `"$mensagem`"" -ForegroundColor Gray
git commit -m $mensagem
if ($LASTEXITCODE -ne 0) {
    Show-ResumoEFechar -CodigoSaida $LASTEXITCODE -MensagemExtra ">> Falha no commit."
}

Write-Host ">> git push origin main" -ForegroundColor Gray
git push origin main
if ($LASTEXITCODE -ne 0) {
    Show-ResumoEFechar -CodigoSaida $LASTEXITCODE -MensagemExtra ">> Falha no push."
}

Write-Host ">> Enviado para o GitHub com sucesso." -ForegroundColor Green
Show-ResumoEFechar -CodigoSaida 0
