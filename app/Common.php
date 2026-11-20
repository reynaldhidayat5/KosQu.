<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('kode')) {
    /** Membuat kode unik, contoh: kode('KOS-', 5) => KOS-A1B2C */
    function kode(string $prefix, int $panjang = 6): string
    {
        return $prefix . strtoupper(substr(bin2hex(random_bytes(8)), 0, $panjang));
    }
}

if (! function_exists('rupiah')) {
    function rupiah($angka): string
    {
        return 'Rp' . number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('rupiah_ringkas')) {
    /** 800000 => 800rb, 1450000 => 1,45jt (dipakai pada pin peta) */
    function rupiah_ringkas($angka): string
    {
        $angka = (float) $angka;
        if ($angka >= 1000000) {
            return 'Rp' . rtrim(rtrim(number_format($angka / 1000000, 2, ',', ''), '0'), ',') . 'jt';
        }
        return 'Rp' . round($angka / 1000) . 'rb';
    }
}

if (! function_exists('badge_status')) {
    function badge_status(?string $status): string
    {
        return match ($status) {
            'Berhasil', 'Dicairkan', 'verified', 'settlement', 'aktif', 'tersedia' => 'ok',
            'Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diajukan', 'pending'    => 'warn',
            'Ditolak', 'Dibatalkan', 'rejected', 'nonaktif', 'penuh'                => 'bad',
            default                                                                 => 'muted',
        };
    }
}

if (! function_exists('foto_kos_url')) {
    function foto_kos_url(?string $file): string
    {
        return $file ? base_url('uploads/kos/' . rawurlencode($file)) : base_url('assets/img/kos-placeholder.svg');
    }
}

if (! function_exists('aman_redirect')) {
    /** Hanya izinkan redirect ke path internal (mencegah open redirect). */
    function aman_redirect(?string $path, string $default = '/'): string
    {
        if ($path && str_starts_with($path, '/') && ! str_starts_with($path, '//') && ! str_contains($path, '\\')) {
            return $path;
        }
        return $default;
    }
}


if (! function_exists('nilai')) {
    /** Nilai isian form: input lama (setelah validasi gagal) atau data bawaan, selalu ter-escape. */
    function nilai(string $key, $default = ''): string
    {
        return (string) (old($key) ?? esc((string) $default));
    }
}
