<?php

namespace App\Controllers\Mitra;

use App\Controllers\BaseController;
use App\Models\MitraModel;
use App\Models\PemesananModel;
use App\Models\TipeKamarModel;

/** Activity Diagram Hal. 8 & Sequence Diagram Hal. 27: Verifikasi pesanan oleh Mitra. */
class PesananController extends BaseController
{
    public function index()
    {
        $mitra = $this->mitraSaatIni();
        $model = new PemesananModel();
        $model->batalkanKedaluwarsa();
        return view('mitra/pesanan_masuk', ['pesanan' => $model->milikMitra($mitra['kd_mitra'])]);
    }

    public function verifikasi($noPesan, $aksi)
    {
        $mitra   = $this->mitraSaatIni();
        $model   = new PemesananModel();
        $pesanan = $model->detail($noPesan);

        // pesanan harus milik kos mitra ini & sedang menunggu verifikasi
        if (! $pesanan || $pesanan['kd_mitra'] !== $mitra['kd_mitra']) {
            return redirect()->to('/mitra/pesanan')->with('error', 'Pesanan tidak ditemukan.');
        }
        if ($pesanan['status_pesan'] !== 'Menunggu Verifikasi') {
            return redirect()->to('/mitra/pesanan')->with('warning', 'Pesanan ini sudah diproses atau belum dibayar.');
        }

        $db = \Config\Database::connect();

        if ($aksi === 'terima') {
            $tipeModel = new TipeKamarModel();
            $tipe      = $tipeModel->find($pesanan['kd_tipe']);
            if (! $tipe || (int) $tipe['kamar_tersedia'] < 1) {
                return redirect()->to('/mitra/pesanan')->with('error', 'Kamar tipe ini sudah penuh. Tolak pesanan atau tambah ketersediaan kamar.');
            }

            $db->transStart();
            $model->update($noPesan, ['status_pesan' => 'Berhasil']);

            // pembayaran dinyatakan lunas setelah diverifikasi
            $db->table('pembayaran')->where('no_pesan', $noPesan)->where('status_bayar', 'pending')->update(['status_bayar' => 'settlement']);

            // kurangi kamar tersedia
            $sisa = (int) $tipe['kamar_tersedia'] - 1;
            $tipeModel->update($tipe['kd_tipe'], ['kamar_tersedia' => $sisa, 'status_tipe' => $sisa === 0 ? 'penuh' : 'tersedia']);

            // saldo mitra bertambah sebesar sub total (biaya layanan menjadi pendapatan platform)
            $mitraModel = new MitraModel();
            $mitraModel->update($mitra['kd_mitra'], ['saldo' => (int) $mitra['saldo'] + (int) $pesanan['sub_total']]);

            // terbitkan e-invoice
            if (! $db->table('invoice')->where('no_pesan', $noPesan)->countAllResults()) {
                $db->table('invoice')->insert([
                    'no_invoice'    => kode('INV-' . date('ymd') . '-', 6),
                    'no_pesan'      => $noPesan,
                    'tgl_terbit'    => date('Y-m-d'),
                    'total_tagihan' => $pesanan['total_bayar'],
                ]);
            }
            $db->transComplete();

            if (! $db->transStatus()) {
                return redirect()->to('/mitra/pesanan')->with('error', 'Gagal memproses pesanan.');
            }
            return redirect()->to('/mitra/pesanan')->with('success', 'Pesanan diterima & e-invoice diterbitkan. Customer dapat melihat statusnya di Riwayat Pesanan.');
        }

        if ($aksi === 'tolak') {
            // Stok kamar baru berkurang saat pesanan DITERIMA, sehingga penolakan tidak mengubah stok.
            // Pembayaran ditandai "rejected" => dana dikembalikan (refund) ke customer.
            $db->transStart();
            $model->update($noPesan, ['status_pesan' => 'Ditolak']);
            $db->table('pembayaran')->where('no_pesan', $noPesan)->update(['status_bayar' => 'rejected']);
            $db->transComplete();
            return redirect()->to('/mitra/pesanan')->with('warning', 'Pesanan ditolak. Pembayaran customer ditandai untuk di-refund.');
        }

        return redirect()->to('/mitra/pesanan')->with('error', 'Aksi tidak dikenal.');
    }
}
