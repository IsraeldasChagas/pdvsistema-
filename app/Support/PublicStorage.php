<?php

namespace App\Support;

/**
 * URLs de arquivos em storage/app/public (expostos via public/storage).
 *
 * Em requisições HTTP, usa o mesmo esquema/host (e subpasta, se houver) da
 * requisição atual, para não depender de APP_URL no .env (comum em HTTPS).
 * Fora de HTTP (ex.: e-mail/console), usa asset().
 */
final class PublicStorage
{
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = ltrim($path, '/');

        if (! app()->runningInConsole() && request()->hasHeader('Host')) {
            $base = rtrim(request()->getSchemeAndHttpHost().request()->getBasePath(), '/');

            return $base.'/storage/'.$path;
        }

        return asset('storage/'.$path);
    }
}
