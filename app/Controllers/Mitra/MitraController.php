<?php

namespace App\Controllers\Mitra;

use App\Controllers\BaseController;
use App\Models\PemesananModel;

class MitraController extends BaseController
{
    public function dashboard()
    {
        $mitra = $this->mitraSaatIni();
        if (! $mitra) {
            return redirect()->to('/')->with('error', 'Data mitra tidak ditemukan.');
        }
        $db = \Config\Database::connect();

        $pesananModel = new PemesananModel();
        $pesananModel->batalkanKedaluwarsa();
        $pesanan = $pesananModel->milikMitra($mitra['kd_mitra']);

        $stats = [
            'total_kos'     => $db->table('kos')->where('kd_mitra', $mitra['kd_mitra'])->countAllResults(),
            'pesanan_masuk' => count(array_filter($pesanan, static fn ($r) => $r['status_pesan'] === 'Menunggu Verifikasi')),
            'saldo'         => (int) $mitra['saldo'],
        ];
        return view('mitra/dashboard', ['stats' => $stats, 'mitra' => $mitra, 'terbaru' => array_slice($pesanan, 0, 5)]);
    }

    // Menu laporan: laporan keuangan mitra
    public function laporan()
    {
        $mitra = $this->mitraSaatIni();
        $dari   = (string) $this->request->getGet('dari');
        $sampai = (string) $this->request->getGet('sampai');
        $db     = \Config\Database::connect();

        $b = $db->table('invoice')
            ->select('invoice.no_invoice, invoice.tgl_terbit, pemesanan.no_pesan, pemesanan.sub_total, pemesanan.biaya_layanan, pemesanan.total_bayar, kos.nm_kos, tipe_kamar.nm_tipe, user.nama_user')
            ->join('pemesanan', 'pemesanan.no_pesan = invoice.no_pesan')
            ->join('tipe_kamar', 'tipe_kamar.kd_tipe = pemesanan.kd_tipe')
            ->join('kos', 'kos.kd_kos = tipe_kamar.kd_kos')
            ->join('user', 'user.id = pemesanan.id')
            ->where('kos.kd_mitra', $mitra['kd_mitra']);
        if ($dari !== '') {
            $b->where('invoice.tgl_terbit >=', $dari);
        }
        if ($sampai !== '') {
            $b->where('invoice.tgl_terbit <=', $sampai);
        }
        $transaksi = $b->orderBy('invoice.tgl_terbit', 'DESC')->get()->getResultArray();

        $tarik = $db->table('penarikan_dana')
            ->select('COALESCE(SUM(jumlah_bersih),0) AS bersih, COALESCE(SUM(komisi),0) AS komisi', false)
            ->where('kd_mitra', $mitra['kd_mitra'])->where('status_tarik', 'Dicairkan')->get()->getRowArray();

        return view('mitra/laporan', [
            'transaksi'  => $transaksi,
            'pendapatan' => array_sum(array_column($transaksi, 'sub_total')),
            'tarik'      => $tarik,
            'saldo'      => (int) $mitra['saldo'],
            'dari'       => $dari,
            'sampai'     => $sampai,
        ]);
    }
}
