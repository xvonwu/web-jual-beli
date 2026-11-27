<?php

namespace App\Support;

/**
 * Data wilayah Indonesia (Kepmendagri No. 300.2.2-2430 Tahun 2025, sumber: github.com/cahyadsn/wilayah, MIT).
 * File: public/data/wilayah.json  -> [{n: provinsi, c: [{n: kota/kabupaten, d: [kecamatan...]}]}]
 */
class Wilayah
{
    private static ?array $data = null;

    public static function all(): array
    {
        return self::$data ??= json_decode(file_get_contents(public_path('data/wilayah.json')), true);
    }

    /** @return string[] */
    public static function provinces(): array
    {
        return array_column(self::all(), 'n');
    }

    /** @return string[] */
    public static function cities(?string $province): array
    {
        foreach (self::all() as $p) {
            if ($p['n'] === $province) {
                return array_column($p['c'], 'n');
            }
        }

        return [];
    }

    /** @return string[] */
    public static function districts(?string $province, ?string $city): array
    {
        foreach (self::all() as $p) {
            if ($p['n'] === $province) {
                foreach ($p['c'] as $c) {
                    if ($c['n'] === $city) {
                        return $c['d'];
                    }
                }
            }
        }

        return [];
    }
}
