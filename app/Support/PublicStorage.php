<?php

namespace App\Support;

/**
 * URLs de arquivos em storage/app/public (expostos via public/storage).
 *
 * Usa caminho relativo "/storage/..." para o navegador sempre pedir no mesmo
 * host/protocolo da página (evita imagem quebrada quando APP_URL está em http
 * e o site em https, ou domínio diferente do .env).
 */
final class PublicStorage
{
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = ltrim($path, '/');

        return '/storage/'.$path;
    }
}
