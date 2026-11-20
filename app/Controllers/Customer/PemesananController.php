<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\PemesananModel;
use App\Models\PengaturanModel;
use App\Models\ReviewModel;
use App\Models\TipeKamarModel;

class PemesananController extends BaseController
{
    // Activity Diagram Hal. 6 & Sequence Diagram Hal. 25: form pemesanan
    // (route sudah dilindungi filter login: belum login -> diarahkan ke Login)
    public function form($kdTipe)
    {
        $tipe = (new TipeKamarModel())->withKos($kdTipe);
        if (! $tipe || $tipe['status_kos'] !== 'aktif' || $tipe['status_kyc'] !== 'verified') {
            return redirect()->to('/')->with('error', 'Tipe kamar tidak ditemukan.');
        }

        return view('customer/form_pesan', [
            'tipe'          => $tipe,
            'biayaLayanan'  => (int) (new PengaturanModel())->ambil('biaya_layanan'),
            'tgl'           => (string) $this->request->getGet('tgl'),
        ]);
    }

    // Menghitung sub total, biaya layanan, total bayar -> menyimpan pemesanan (status Menunggu Pembayaran)
    public function buat()
    {
        $tipeModel = new TipeKamarModel();
        $kdTipe    = (string) $this->request->getPost('kd_tipe');
        $tipe      = $tipeModel->withKos($kdTipe);

        $balik = '/customer/pesan/' . rawurlencode($kdTipe);
        if (! $tipe || $tipe['status_kos'] !== 'aktif' || $tipe['status_kyc'] !== 'verified') {
            return redirect()->to('/')->with('error', 'Tipe kamar tidak ditemukan.');
        }

        $rules = [
            'tgl_mulai'     => 'required|valid_date[Y-m-d]',
            'durasi'        => 'required|is_natural_no_zero|less_than_equal_to[365]',
            'satuan_durasi' => 'required|in_list[harian,bulanan,tahunan]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->to($balik)->withInput()->with('errors', $this->validator->getErrors());
        }

        $tglMulai = (string) $this->request->getPost('tgl_mulai');
        if ($tglMulai < date('Y-m-d')) {
            return redirect()->to($balik)->withInput()->with('error', 'Tanggal masuk tidak boleh sebelum hari ini.');
        }
        if ((int) $tipe['kamar_tersedia'] < 1 || $tipe['status_tipe'] === 'penuh') {
            return redirect()->to($balik)->with('error', 'Maaf, kamar tipe ini sudah penuh.');
        }

        $durasi = (int) $this->request->getPost('durasi');
        $satuan = (string) $this->request->getPost('satuan_durasi');
        $harga  = $tipeModel->hargaSatuan($tipe, $satuan);
        if ($harga <= 0) {
            return redirect()->to($balik)->withInput()->with('error', 'Harga untuk satuan sewa tersebut belum diatur oleh pemilik kos.');
        }

        $subTotal     = $harga * $durasi;
        $biayaLayanan = (int) (new PengaturanModel())->ambil('biaya_layanan');
        $noPesan      = kode('PSN-' . date('ymd') . '-', 6);

        (new PemesananModel())->insert([
            'no_pesan'      => $noPesan,
            'id'            => (int) session()->get('id'),
            'kd_tipe'       => $kdTipe,
            'tgl_mulai'     => $tglMulai,
            'durasi'        => $durasi,
            'satuan_durasi' => $satuan,
            'sub_total'     => $subTotal,
            'biaya_layanan' => $biayaLayanan,
            'total_bayar'   => $subTotal + $biayaLayanan,
            'batas_bayar'   => date('Y-m-d H:i:s', strtotime('+24 hours')),
            'status_pesan'  => 'Menunggu Pembayaran',
        ]);

        return redirect()->to('/customer/bayar/' . $noPesan)->with('success', 'Pesanan dibuat. Selesaikan pembayaran sebelum batas waktu.');
    }

    public function batalkan($noPesan)
    {
        $model   = new PemesananModel();
        $pesanan = $model->find($noPesan);
        if (! $pesanan || (int) $pesanan['id'] !== (int) session()->get('id')) {
            return redirect()->to('/customer/riwayat')->with('error', 'Pesanan tidak ditemukan.');
        }
        if ($pesanan['status_pesan'] !== 'Menunggu Pembayaran') {
            return redirect()->to('/customer/riwayat')->with('error', 'Hanya pesanan yang belum dibayar yang dapat dibatalkan.');
        }
        $model->update($noPesan, ['status_pesan' => 'Dibatalkan']);
        return redirect()->to('/customer/riwayat')->with('success', 'Pesanan dibatalkan.');
    }

    // Menu riwayat pesanan: memantau status pesanan
    public function riwayat()
    {
        $model = new PemesananModel();
        $model->batalkanKedaluwarsa();

        return view('customer/riwayat', [
            'pesanan' => $model->milikCustomer((int) session()->get('id')),
        ]);
    }

    // E-kuitansi (invoice) - halaman siap cetak / simpan sebagai PDF
    public function invoice($noPesan)
    {
        $pesanan = (new PemesananModel())->detail($noPesan);
        if (! $pesanan || (int) $pesanan['id'] !== (int) session()->get('id') || $pesanan['status_pesan'] !== 'Berhasil') {
            return redirect()->to('/customer/riwayat')->with('error', 'E-kuitansi belum tersedia untuk pesanan ini.');
        }
        $invoice = \Config\Database::connect()->table('invoice')->where('no_pesan', $noPesan)->get()->getRowArray();
        if (! $invoice) {
            return redirect()->to('/customer/riwayat')->with('error', 'Invoice belum diterbitkan.');
        }
        return view('customer/invoice', ['p' => $pesanan, 'invoice' => $invoice]);
    }

    // Memberi review (V2)
    public function review($noPesan)
    {
        $pesanan = (new PemesananModel())->find($noPesan);
        if (! $pesanan || (int) $pesanan['id'] !== (int) session()->get('id') || $pesanan['status_pesan'] !== 'Berhasil') {
            return redirect()->to('/customer/riwayat')->with('error', 'Review hanya untuk pesanan yang berhasil.');
        }
        $rules = ['rating' => 'required|in_list[1,2,3,4,5]', 'komentar' => 'required|max_length[500]'];
        if (! $this->validate($rules)) {
            return redirect()->to('/customer/riwayat')->with('error', 'Isi rating (1-5) dan komentar ulasan.');
        }
        $reviewModel = new ReviewModel();
        if ($reviewModel->where('no_pesan', $noPesan)->first()) {
            return redirect()->to('/customer/riwayat')->with('error', 'Pesanan ini sudah Anda ulas.');
        }
        $reviewModel->insert([
            'kd_review'  => kode('RV-', 6),
            'no_pesan'   => $noPesan,
            'rating'     => (int) $this->request->getPost('rating'),
            'komentar'   => $this->request->getPost('komentar'),
            'tgl_review' => date('Y-m-d'),
        ]);
        return redirect()->to('/customer/riwayat')->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
