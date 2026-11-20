<?php

namespace App\Controllers\Mitra;

use App\Controllers\BaseController;
use App\Models\KosModel;
use App\Models\TipeKamarModel;

/** Mengelola tipe kamar, harga & ketersediaan kamar. */
class KamarController extends BaseController
{
    public function index($kdTipe = null)
    {
        $mitra = $this->mitraSaatIni();
        $model = new TipeKamarModel();

        $tipe = null;
        if ($kdTipe) {
            $tipe = $model->withKos($kdTipe);
            if (! $tipe || $tipe['kd_mitra'] !== $mitra['kd_mitra']) {
                return redirect()->to('/mitra/kamar')->with('error', 'Tipe kamar tidak ditemukan.');
            }
        }

        return view('mitra/kelola_kamar', [
            'tipe'       => $tipe,
            'daftarKos'  => (new KosModel())->where('kd_mitra', $mitra['kd_mitra'])->orderBy('nm_kos', 'ASC')->findAll(),
            'daftarTipe' => $model->milikMitra($mitra['kd_mitra']),
        ]);
    }

    public function simpan()
    {
        $mitra = $this->mitraSaatIni();
        $model = new TipeKamarModel();

        $rules = [
            'kd_kos'         => 'required',
            'nm_tipe'        => 'required|max_length[30]',
            'jml_kamar'      => 'required|is_natural',
            'kamar_tersedia' => 'required|is_natural',
            'harga_harian'   => 'required|is_natural',
            'harga_bulanan'  => 'required|is_natural',
            'harga_tahunan'  => 'required|is_natural',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kos = (new KosModel())->where('kd_kos', $this->request->getPost('kd_kos'))->where('kd_mitra', $mitra['kd_mitra'])->first();
        if (! $kos) {
            return redirect()->back()->withInput()->with('error', 'Properti tidak valid.');
        }

        $jml       = (int) $this->request->getPost('jml_kamar');
        $tersedia  = min($jml, (int) $this->request->getPost('kamar_tersedia'));
        $data = [
            'kd_kos'         => $kos['kd_kos'],
            'nm_tipe'        => $this->request->getPost('nm_tipe'),
            'harga_harian'   => (int) $this->request->getPost('harga_harian'),
            'harga_bulanan'  => (int) $this->request->getPost('harga_bulanan'),
            'harga_tahunan'  => (int) $this->request->getPost('harga_tahunan'),
            'jml_kamar'      => $jml,
            'kamar_tersedia' => $tersedia,
            'status_tipe'    => $tersedia > 0 ? 'tersedia' : 'penuh',
        ];

        $kdTipe = (string) $this->request->getPost('kd_tipe');
        if ($kdTipe !== '') {
            $lama = $model->withKos($kdTipe);
            if (! $lama || $lama['kd_mitra'] !== $mitra['kd_mitra']) {
                return redirect()->to('/mitra/kamar')->with('error', 'Tipe kamar tidak ditemukan.');
            }
            $model->update($kdTipe, $data);
        } else {
            $model->insert(['kd_tipe' => kode('TP-', 5)] + $data);
        }
        return redirect()->to('/mitra/kamar')->with('success', 'Tipe kamar berhasil disimpan.');
    }

    public function hapus($kdTipe)
    {
        $mitra = $this->mitraSaatIni();
        $model = new TipeKamarModel();
        $tipe  = $model->withKos($kdTipe);
        if (! $tipe || $tipe['kd_mitra'] !== $mitra['kd_mitra']) {
            return redirect()->to('/mitra/kamar')->with('error', 'Tipe kamar tidak ditemukan.');
        }
        $ada = \Config\Database::connect()->table('pemesanan')->where('kd_tipe', $kdTipe)->countAllResults();
        if ($ada > 0) {
            return redirect()->to('/mitra/kamar')->with('warning', 'Tipe kamar sudah memiliki pesanan, tidak dapat dihapus. Atur kamar tersedia menjadi 0.');
        }
        $model->delete($kdTipe);
        return redirect()->to('/mitra/kamar')->with('success', 'Tipe kamar dihapus.');
    }
}
