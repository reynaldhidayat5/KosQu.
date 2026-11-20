<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MitraModel;
use App\Models\PenarikanDanaModel;
use App\Models\PengaturanModel;

/** Activity Diagram Hal. 12 & Sequence Diagram Hal. 31: Memproses penarikan dana (payout). */
class PayoutController extends BaseController
{
    public function index()
    {
        $daftar = (new PenarikanDanaModel())
            ->select('penarikan_dana.*, user.nama_user, mitra.nama_bank, mitra.no_rekening, mitra.atas_nama')
            ->join('mitra', 'mitra.kd_mitra = penarikan_dana.kd_mitra')
            ->join('user', 'user.id = mitra.id')
            ->orderBy("FIELD(penarikan_dana.status_tarik, 'Diajukan', 'Dicairkan', 'Ditolak')", '', false)
            ->orderBy('penarikan_dana.tgl_ajuan', 'DESC')
            ->findAll();

        return view('admin/payout', [
            'payoutList' => $daftar,
            'komisi'     => (float) (new PengaturanModel())->ambil('komisi_persen'),
        ]);
    }

    public function proses($kdTarik, $aksi)
    {
        $tarikModel = new PenarikanDanaModel();
        $tarik      = $tarikModel->find($kdTarik);

        if (! $tarik || $tarik['status_tarik'] !== 'Diajukan') {
            return redirect()->to('/admin/payout')->with('warning', 'Pengajuan tidak ditemukan atau sudah diproses.');
        }

        $mitraModel = new MitraModel();
        $mitra      = $mitraModel->find($tarik['kd_mitra']);
        $db         = \Config\Database::connect();

        if ($aksi === 'setujui') {
            $persen = (float) (new PengaturanModel())->ambil('komisi_persen');
            $komisi = (int) round($tarik['jumlah_tarik'] * $persen / 100);
            $bersih = (int) $tarik['jumlah_tarik'] - $komisi;

            // Saldo mitra sudah ditahan saat pengajuan, jadi cukup mengubah status & mencatat komisi.
            $tarikModel->update($kdTarik, [
                'id'            => (int) session()->get('id'),
                'komisi'        => $komisi,
                'jumlah_bersih' => $bersih,
                'status_tarik'  => 'Dicairkan',
                'tgl_proses'    => date('Y-m-d'),
            ]);
            return redirect()->to('/admin/payout')->with('success', 'Payout disetujui. Transfer ' . rupiah($bersih) . ' ke ' . ($mitra['nama_bank'] ?? '') . ' ' . ($mitra['no_rekening'] ?? '') . ' (komisi ' . rupiah($komisi) . ').');
        }

        if ($aksi === 'tolak') {
            $db->transStart();
            if ($mitra) {
                $mitraModel->update($mitra['kd_mitra'], ['saldo' => (int) $mitra['saldo'] + (int) $tarik['jumlah_tarik']]);
            }
            $tarikModel->update($kdTarik, ['id' => (int) session()->get('id'), 'status_tarik' => 'Ditolak', 'tgl_proses' => date('Y-m-d')]);
            $db->transComplete();
            return redirect()->to('/admin/payout')->with('warning', 'Pengajuan ditolak dan saldo dikembalikan ke mitra.');
        }

        return redirect()->to('/admin/payout')->with('error', 'Aksi tidak dikenal.');
    }
}
