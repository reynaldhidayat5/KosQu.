<?php

namespace App\Models;

use CodeIgniter\Model;

class PemesananModel extends Model
{
    protected $table            = 'pemesanan';
    protected $primaryKey       = 'no_pesan';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['no_pesan', 'id', 'kd_tipe', 'tgl_mulai', 'durasi', 'satuan_durasi', 'sub_total', 'biaya_layanan', 'total_bayar', 'batas_bayar', 'status_pesan'];

    private const SELECT = 'pemesanan.*, tipe_kamar.nm_tipe, tipe_kamar.kd_kos, kos.nm_kos, kos.alamat_kos, kos.kota, kos.kd_mitra, '
        . 'user.nama_user, user.email, user.no_telp, '
        . '(SELECT metode_bayar FROM pembayaran WHERE pembayaran.no_pesan = pemesanan.no_pesan ORDER BY tgl_bayar DESC, no_bayar DESC LIMIT 1) AS metode_bayar, '
        . '(SELECT status_bayar FROM pembayaran WHERE pembayaran.no_pesan = pemesanan.no_pesan ORDER BY tgl_bayar DESC, no_bayar DESC LIMIT 1) AS status_bayar, '
        . '(SELECT bukti_transfer FROM pembayaran WHERE pembayaran.no_pesan = pemesanan.no_pesan ORDER BY tgl_bayar DESC, no_bayar DESC LIMIT 1) AS bukti_transfer, '
        . '(SELECT ref_gateway FROM pembayaran WHERE pembayaran.no_pesan = pemesanan.no_pesan ORDER BY tgl_bayar DESC, no_bayar DESC LIMIT 1) AS ref_gateway, '
        . '(SELECT COUNT(*) FROM review WHERE review.no_pesan = pemesanan.no_pesan) AS sudah_review, '
        . '(SELECT no_invoice FROM invoice WHERE invoice.no_pesan = pemesanan.no_pesan LIMIT 1) AS no_invoice';

    private function dasar()
    {
        return $this->select(self::SELECT, false)
            ->join('tipe_kamar', 'tipe_kamar.kd_tipe = pemesanan.kd_tipe')
            ->join('kos', 'kos.kd_kos = tipe_kamar.kd_kos')
            ->join('user', 'user.id = pemesanan.id');
    }

    /** Pesanan yang melewati batas bayar otomatis dibatalkan. */
    public function batalkanKedaluwarsa(): void
    {
        $this->db->table('pemesanan')
            ->where('status_pesan', 'Menunggu Pembayaran')
            ->where('batas_bayar <', date('Y-m-d H:i:s'))
            ->update(['status_pesan' => 'Dibatalkan']);
    }

    public function detail(string $noPesan): ?array
    {
        return $this->dasar()->where('pemesanan.no_pesan', $noPesan)->first();
    }

    public function milikCustomer(int $idUser): array
    {
        return $this->dasar()->where('pemesanan.id', $idUser)->orderBy('pemesanan.batas_bayar', 'DESC')->findAll();
    }

    public function milikMitra(string $kdMitra): array
    {
        return $this->dasar()->where('kos.kd_mitra', $kdMitra)->orderBy('pemesanan.batas_bayar', 'DESC')->findAll();
    }

    public function semua(?string $status = null): array
    {
        $b = $this->dasar();
        if ($status) {
            $b->where('pemesanan.status_pesan', $status);
        }
        return $b->orderBy('pemesanan.batas_bayar', 'DESC')->findAll();
    }
}
