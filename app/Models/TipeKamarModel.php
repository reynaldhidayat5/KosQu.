<?php

namespace App\Models;

use CodeIgniter\Model;

class TipeKamarModel extends Model
{
    protected $table            = 'tipe_kamar';
    protected $primaryKey       = 'kd_tipe';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['kd_tipe', 'kd_kos', 'nm_tipe', 'harga_harian', 'harga_bulanan', 'harga_tahunan', 'jml_kamar', 'kamar_tersedia', 'status_tipe'];

    /** Harga satuan sesuai satuan durasi (harian/bulanan/tahunan). */
    public function hargaSatuan(array $tipe, string $satuan): int
    {
        return (int) match ($satuan) {
            'harian'  => $tipe['harga_harian'],
            'tahunan' => $tipe['harga_tahunan'],
            default   => $tipe['harga_bulanan'],
        };
    }

    /** Class Diagram: hitungHarga() */
    public function hitungHarga(string $kdTipe, int $durasi, string $satuan): int
    {
        $tipe = $this->find($kdTipe);
        if (! $tipe) {
            return 0;
        }
        return $this->hargaSatuan($tipe, $satuan) * max(0, $durasi);
    }

    /** Tipe kamar beserta nama kos (untuk halaman mitra). */
    public function milikMitra(string $kdMitra): array
    {
        return $this->select('tipe_kamar.*, kos.nm_kos')
            ->join('kos', 'kos.kd_kos = tipe_kamar.kd_kos')
            ->where('kos.kd_mitra', $kdMitra)
            ->orderBy('kos.nm_kos', 'ASC')->orderBy('tipe_kamar.nm_tipe', 'ASC')
            ->findAll();
    }

    /** Tipe kamar + kos + mitra pemiliknya (untuk cek kepemilikan & form pemesanan). */
    public function withKos(string $kdTipe): ?array
    {
        return $this->select('tipe_kamar.*, kos.nm_kos, kos.alamat_kos, kos.kota, kos.kd_mitra, kos.status_kos, mitra.status_kyc')
            ->join('kos', 'kos.kd_kos = tipe_kamar.kd_kos')
            ->join('mitra', 'mitra.kd_mitra = kos.kd_mitra')
            ->where('tipe_kamar.kd_tipe', $kdTipe)
            ->first();
    }
}
