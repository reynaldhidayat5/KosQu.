<?php

namespace App\Controllers\Mitra;

use App\Controllers\BaseController;
use App\Models\MitraModel;
use App\Models\PenarikanDanaModel;
use App\Models\PengaturanModel;

/** Activity Diagram Hal. 11 & Sequence Diagram Hal. 30: Pengajuan penarikan dana. */
class PayoutController extends BaseController
{
    public function index()
    {
        $mitra = $this->mitraSaatIni();
        return view('mitra/penarikan_dana', [
            'mitra'   => $mitra,
            'saldo'   => (int) $mitra['saldo'],
            'komisi'  => (float) (new PengaturanModel())->ambil('komisi_persen'),
            'riwayat' => (new PenarikanDanaModel())->where('kd_mitra', $mitra['kd_mitra'])->orderBy('tgl_ajuan', 'DESC')->orderBy('kd_tarik', 'DESC')->findAll(),
        ]);
    }

    public function ajukan()
    {
        $mitra = $this->mitraSaatIni();
        if ($mitra['status_kyc'] !== 'verified') {
            return redirect()->to('/mitra/penarikan')->with('error', 'Penarikan dana hanya untuk mitra yang sudah terverifikasi KYC.');
        }
        if (! $this->validate(['jumlah_tarik' => 'required|is_natural_no_zero'])) {
            return redirect()->back()->with('error', 'Masukkan jumlah penarikan yang valid.');
        }

        $jumlah = (int) $this->request->getPost('jumlah_tarik');
        if ($jumlah > (int) $mitra['saldo']) {
            return redirect()->to('/mitra/penarikan')->with('error', 'Saldo tidak mencukupi!');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        // saldo ditahan sampai admin memproses (dikembalikan bila ditolak)
        (new MitraModel())->update($mitra['kd_mitra'], ['saldo' => (int) $mitra['saldo'] - $jumlah]);
        (new PenarikanDanaModel())->insert([
            'kd_tarik'     => kode('WD-', 6),
            'kd_mitra'     => $mitra['kd_mitra'],
            'jumlah_tarik' => $jumlah,
            'status_tarik' => 'Diajukan',
            'tgl_ajuan'    => date('Y-m-d'),
        ]);
        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to('/mitra/penarikan')->with('error', 'Pengajuan gagal disimpan.');
        }
        return redirect()->to('/mitra/penarikan')->with('success', 'Pengajuan penarikan dana berhasil dikirim ke Admin.');
    }
}
