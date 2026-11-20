<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\PemesananModel;
use App\Models\PengaturanModel;

/**
 * Activity Diagram Hal. 7 & Sequence Diagram Hal. 26.
 * Metode: transfer_manual (unggah bukti) atau va / ewallet (Payment Gateway - simulasi sandbox).
 */
class PembayaranController extends BaseController
{
    /** Ambil pesanan milik customer yang sedang login & masih bisa dibayar. */
    private function pesananBisaBayar(string $noPesan): ?array
    {
        $model = new PemesananModel();
        $model->batalkanKedaluwarsa();
        $p = $model->detail($noPesan);
        if (! $p || (int) $p['id'] !== (int) session()->get('id')) {
            return null;
        }
        return $p;
    }

    private function pembayaranTerakhir(string $noPesan): ?array
    {
        return (new PembayaranModel())->where('no_pesan', $noPesan)->orderBy('no_bayar', 'DESC')->first();
    }

    // Form pembayaran: pilih metode
    public function form($noPesan)
    {
        $p = $this->pesananBisaBayar($noPesan);
        if (! $p) {
            return redirect()->to('/customer/riwayat')->with('error', 'Pesanan tidak ditemukan.');
        }
        if ($p['status_pesan'] !== 'Menunggu Pembayaran') {
            return redirect()->to('/customer/riwayat')->with('info', 'Pesanan ini berstatus ' . $p['status_pesan'] . '.');
        }
        return view('customer/bayar', ['p' => $p]);
    }

    // "Memproses permintaan": transfer manual? ya -> rekening tujuan & form unggah bukti; tidak -> buat transaksi gateway
    public function pilihMetode($noPesan)
    {
        $p = $this->pesananBisaBayar($noPesan);
        if (! $p || $p['status_pesan'] !== 'Menunggu Pembayaran') {
            return redirect()->to('/customer/riwayat')->with('error', 'Pesanan tidak dapat dibayar.');
        }
        $metode = (string) $this->request->getPost('metode_bayar');
        if (! in_array($metode, ['transfer_manual', 'va', 'ewallet'], true)) {
            return redirect()->back()->with('error', 'Pilih metode pembayaran.');
        }

        $bayarModel = new PembayaranModel();
        $ada        = $bayarModel->where('no_pesan', $noPesan)->where('status_bayar', 'pending')->first();

        if ($metode !== 'transfer_manual') {
            // buat transaksi ke Payment Gateway (simulasi): nomor VA / URL bayar e-wallet
            $ref = $metode === 'va'
                ? '8808' . str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT)
                : 'EW-' . strtoupper(bin2hex(random_bytes(6)));
            $baris = [
                'metode_bayar'   => $metode,
                'tgl_bayar'      => date('Y-m-d'),
                'jumlah_bayar'   => $p['total_bayar'],
                'bukti_transfer' => null,
                'ref_gateway'    => $ref,
                'status_bayar'   => 'pending',
            ];
        } else {
            $baris = ['metode_bayar' => 'transfer_manual', 'tgl_bayar' => date('Y-m-d'), 'jumlah_bayar' => $p['total_bayar'], 'ref_gateway' => null, 'status_bayar' => 'pending'];
        }

        if ($ada) {
            $bayarModel->update($ada['no_bayar'], $baris);
        } else {
            $bayarModel->insert(['no_bayar' => kode('PAY-' . date('ymd'), 6), 'no_pesan' => $noPesan] + $baris);
        }
        return redirect()->to('/customer/bayar/' . rawurlencode($noPesan) . '/instruksi');
    }

    // Menampilkan rekening tujuan + form unggah bukti, atau nomor VA / e-wallet
    public function instruksi($noPesan)
    {
        $p = $this->pesananBisaBayar($noPesan);
        $bayar = $p ? $this->pembayaranTerakhir($noPesan) : null;
        if (! $p || ! $bayar) {
            return redirect()->to('/customer/riwayat')->with('error', 'Pilih metode pembayaran terlebih dahulu.');
        }
        return view('customer/bayar_instruksi', [
            'p'         => $p,
            'bayar'     => $bayar,
            'rekening'  => (new PengaturanModel())->ambil('rekening_platform'),
        ]);
    }

    // Transfer manual: unggah bukti transfer -> status pesanan "Menunggu Verifikasi" (diverifikasi Mitra)
    public function unggahBukti($noPesan)
    {
        $p = $this->pesananBisaBayar($noPesan);
        if (! $p || $p['status_pesan'] !== 'Menunggu Pembayaran') {
            return redirect()->to('/customer/riwayat')->with('error', 'Pesanan tidak dapat dibayar.');
        }
        $rules = ['bukti_transfer' => 'uploaded[bukti_transfer]|is_image[bukti_transfer]|max_size[bukti_transfer,3072]'];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Unggah bukti transfer berupa gambar (maks. 3 MB).');
        }

        $nama = $this->simpanGambar($this->request->getFile('bukti_transfer'), 'bukti');
        $bayarModel = new PembayaranModel();
        $bayar      = $bayarModel->where('no_pesan', $noPesan)->where('status_bayar', 'pending')->first();
        $baris = [
            'metode_bayar'   => 'transfer_manual',
            'tgl_bayar'      => date('Y-m-d'),
            'jumlah_bayar'   => $p['total_bayar'],
            'bukti_transfer' => $nama,
            'status_bayar'   => 'pending',
        ];
        if ($bayar) {
            $bayarModel->update($bayar['no_bayar'], $baris);
        } else {
            $bayarModel->insert(['no_bayar' => kode('PAY-' . date('ymd'), 6), 'no_pesan' => $noPesan] + $baris);
        }

        (new PemesananModel())->update($noPesan, ['status_pesan' => 'Menunggu Verifikasi']);
        return redirect()->to('/customer/riwayat')->with('success', 'Bukti transfer diunggah. Menunggu verifikasi Mitra.');
    }

    /**
     * Simulasi webhook Payment Gateway (status "settlement").
     * Pada produksi, blok ini dipindah ke endpoint webhook Midtrans/Xendit dengan verifikasi signature.
     */
    public function simulasiGateway($noPesan)
    {
        $p = $this->pesananBisaBayar($noPesan);
        $bayar = $p ? $this->pembayaranTerakhir($noPesan) : null;
        if (! $p || ! $bayar || $p['status_pesan'] !== 'Menunggu Pembayaran' || $bayar['metode_bayar'] === 'transfer_manual') {
            return redirect()->to('/customer/riwayat')->with('error', 'Transaksi gateway tidak ditemukan.');
        }
        (new PembayaranModel())->update($bayar['no_bayar'], ['status_bayar' => 'settlement', 'tgl_bayar' => date('Y-m-d')]);
        (new PemesananModel())->update($noPesan, ['status_pesan' => 'Menunggu Verifikasi']);
        return redirect()->to('/customer/riwayat')->with('success', 'Pembayaran diterima (settlement). Menunggu konfirmasi Mitra.');
    }
}
