<?php

namespace App\Support;

/**
 * Ukuran asli gambar di folder public, untuk atribut width/height.
 * Browser memakai ukuran ini untuk memesan ruang sebelum gambar selesai dimuat,
 * jadi tata letak tidak bergeser.
 */
class Img
{
    private static array $cache = [];

    /** @return array{0:int,1:int} */
    public static function size(?string $path, int $fallback = 600): array
    {
        if (! $path || str_starts_with($path, 'http')) {
            return [$fallback, $fallback];
        }

        $path = ltrim(parse_url($path, PHP_URL_PATH) ?? $path, '/');

        return self::$cache[$path] ??= (function () use ($path, $fallback) {
            $file = public_path($path);
            $info = is_file($file) ? @getimagesize($file) : false;

            return $info ? [$info[0], $info[1]] : [$fallback, $fallback];
        })();
    }

    /** Atribut HTML siap pakai: width="…" height="…" */
    public static function attrs(?string $path, int $fallback = 600): string
    {
        [$w, $h] = self::size($path, $fallback);

        return 'width="'.$w.'" height="'.$h.'"';
    }
}
