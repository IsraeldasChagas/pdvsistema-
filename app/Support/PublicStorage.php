<?php

namespace App\Support;

/**
 * URLs de arquivos em storage/app/public (expostos via public/storage).
 * Usa asset() para seguir o host atual e APP_URL/ASSET_URL corretamente.
 */
final class PublicStorage
{
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = ltrim($path, '/');

        return asset('storage/'.$path);
    }
}
