<?php

namespace App\Models;

use CodeIgniter\Model;

class KosModel extends Model
{
    protected $table            = 'kos';
    protected $primaryKey       = 'kd_kos';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['kd_kos', 'kd_mitra', 'nm_kos', 'alamat_kos', 'kota', 'latitude', 'longitude', 'deskripsi', 'aturan_kos', 'tipe_kos', 'status_kos'];

    /**
     * Sequence Diagram Hal. 24: pencarian kos (kata kunci + filter harga, fasilitas, tipe kos).
     * Hanya kos berstatus aktif milik mitra yang KYC-nya terverifikasi.
     */
    public function searchKos(?string $keyword = null, ?string $tipe = null, $maxHarga = null, array $fasilitas = []): array
    {
        $select = 'kos.*, '
            . 'MIN(NULLIF(tipe_kamar.harga_bulanan, 0)) AS harga_mulai, '
            . 'COALESCE(SUM(tipe_kamar.kamar_tersedia), 0) AS total_tersedia, '
            . '(SELECT url_foto FROM foto_kos WHERE foto_kos.kd_kos = kos.kd_kos ORDER BY urutan ASC LIMIT 1) AS foto_utama, '
            . '(SELECT ROUND(AVG(r.rating), 1) FROM review r JOIN pemesanan p ON p.no_pesan = r.no_pesan '
            . 'JOIN tipe_kamar t ON t.kd_tipe = p.kd_tipe WHERE t.kd_kos = kos.kd_kos) AS rating_avg';

        $builder = $this->select($select, false)
            ->join('tipe_kamar', 'tipe_kamar.kd_kos = kos.kd_kos', 'left')
            ->join('mitra', 'mitra.kd_mitra = kos.kd_mitra')
            ->where('kos.status_kos', 'aktif')
            ->where('mitra.status_kyc', 'verified')
            ->groupBy('kos.kd_kos');

        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('kos.nm_kos', $keyword)
                ->orLike('kos.kota', $keyword)
                ->orLike('kos.alamat_kos', $keyword)
                ->groupEnd();
        }
        if (in_array($tipe, ['putra', 'putri', 'campur'], true)) {
            $builder->where('kos.tipe_kos', $tipe);
        }
        if ($maxHarga !== null && $maxHarga !== '' && (int) $maxHarga > 0) {
            $builder->having('harga_mulai <=', (int) $maxHarga);
        }

        $fasilitas = array_values(array_filter($fasilitas, static fn ($f) => is_string($f) && $f !== ''));
        if ($fasilitas) {
            $in = implode(',', array_map([$this->db, 'escape'], $fasilitas));
            $builder->where(
                "kos.kd_kos IN (SELECT kd_kos FROM detail_fasilitas WHERE kd_fas IN ({$in}) GROUP BY kd_kos HAVING COUNT(DISTINCT kd_fas) = " . count($fasilitas) . ')',
                null,
                false
            );
        }

        return $builder->orderBy('kos.nm_kos', 'ASC')->findAll();
    }

    /** Detail kos untuk halaman publik (hanya aktif & mitra terverifikasi). */
    public function detailPublik(string $kdKos): ?array
    {
        return $this->select('kos.*, user.nama_user AS nama_pemilik, mitra.status_kyc')
            ->join('mitra', 'mitra.kd_mitra = kos.kd_mitra')
            ->join('user', 'user.id = mitra.id')
            ->where('kos.kd_kos', $kdKos)
            ->where('kos.status_kos', 'aktif')
            ->where('mitra.status_kyc', 'verified')
            ->first();
    }

    public function fasilitasKos(string $kdKos): array
    {
        return $this->db->table('detail_fasilitas')
            ->select('fasilitas.*')
            ->join('fasilitas', 'fasilitas.kd_fas = detail_fasilitas.kd_fas')
            ->where('detail_fasilitas.kd_kos', $kdKos)
            ->get()->getResultArray();
    }

    public function fotoKos(string $kdKos): array
    {
        return $this->db->table('foto_kos')->where('kd_kos', $kdKos)->orderBy('urutan', 'ASC')->get()->getResultArray();
    }

    /** Daftar kos milik satu mitra, lengkap dengan jumlah tipe kamar. */
    public function milikMitra(string $kdMitra, ?string $cari = null): array
    {
        $builder = $this->select('kos.*, (SELECT COUNT(*) FROM tipe_kamar WHERE tipe_kamar.kd_kos = kos.kd_kos) AS jml_tipe', false)
            ->where('kos.kd_mitra', $kdMitra);
        if ($cari) {
            $builder->groupStart()->like('kos.nm_kos', $cari)->orLike('kos.kota', $cari)->groupEnd();
        }
        return $builder->orderBy('kos.nm_kos', 'ASC')->findAll();
    }
}
