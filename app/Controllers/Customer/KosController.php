<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\FasilitasModel;
use App\Models\KosModel;
use App\Models\TipeKamarModel;

class KosController extends BaseController
{
    // Activity Diagram Hal. 5 & Sequence Diagram Hal. 24: pencarian kos
    public function index()
    {
        $keyword   = trim((string) $this->request->getGet('keyword'));
        $tipe      = (string) $this->request->getGet('tipe');
        $maxHarga  = $this->request->getGet('max_harga');
        $fasilitas = (array) $this->request->getGet('fasilitas');

        $data = [
            'kos'       => (new KosModel())->searchKos($keyword, $tipe, $maxHarga, $fasilitas),
            'daftarFas' => (new FasilitasModel())->orderBy('nm_fas', 'ASC')->findAll(),
            'keyword'   => $keyword,
            'tipe'      => $tipe,
            'maxHarga'  => $maxHarga,
            'fasilitas' => $fasilitas,
            'tgl'       => (string) $this->request->getGet('tgl'),
        ];
        return view('customer/beranda', $data);
    }

    // Halaman detail kos: foto, peta, harga, kamar, fasilitas
    public function detail($kdKos)
    {
        $kosModel = new KosModel();
        $kos      = $kosModel->detailPublik($kdKos);

        if (! $kos) {
            return redirect()->to('/')->with('error', 'Data kos tidak ditemukan atau belum aktif.');
        }

        $db = \Config\Database::connect();
        $ulasan = $db->table('review')
            ->select('review.*, user.nama_user')
            ->join('pemesanan', 'pemesanan.no_pesan = review.no_pesan')
            ->join('tipe_kamar', 'tipe_kamar.kd_tipe = pemesanan.kd_tipe')
            ->join('user', 'user.id = pemesanan.id')
            ->where('tipe_kamar.kd_kos', $kdKos)
            ->orderBy('review.tgl_review', 'DESC')->limit(10)
            ->get()->getResultArray();

        return view('customer/detail_kos', [
            'kos'        => $kos,
            'tipe_kamar' => (new TipeKamarModel())->where('kd_kos', $kdKos)->orderBy('harga_bulanan', 'ASC')->findAll(),
            'fasilitas'  => $kosModel->fasilitasKos($kdKos),
            'foto'       => $kosModel->fotoKos($kdKos),
            'ulasan'     => $ulasan,
            'tgl'        => (string) $this->request->getGet('tgl'),
        ]);
    }
}
