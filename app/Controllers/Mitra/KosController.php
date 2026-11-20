<?php

namespace App\Controllers\Mitra;

use App\Controllers\BaseController;
use App\Models\DetailFasilitasModel;
use App\Models\FasilitasModel;
use App\Models\FotoKosModel;
use App\Models\KosModel;
use App\Models\TipeKamarModel;

/** Activity Diagram Hal. 9 & Sequence Diagram Hal. 29: Mengelola data kos (tambah / cari / ubah / hapus). */
class KosController extends BaseController
{
    public function index($kdKos = null)
    {
        $mitra    = $this->mitraSaatIni();
        $kosModel = new KosModel();
        $cari     = trim((string) $this->request->getGet('cari'));

        $kos = null;
        if ($kdKos) {
            $kos = $kosModel->where('kd_kos', $kdKos)->where('kd_mitra', $mitra['kd_mitra'])->first();
            if (! $kos) {
                return redirect()->to('/mitra/kos')->with('error', 'Data kos tidak ditemukan.');
            }
        }

        return view('mitra/kelola_kos', [
            'mitra'         => $mitra,
            'kos'           => $kos,
            'daftarKos'     => $kosModel->milikMitra($mitra['kd_mitra'], $cari),
            'cari'          => $cari,
            'daftarFas'     => (new FasilitasModel())->orderBy('nm_fas', 'ASC')->findAll(),
            'fasilitasKos'  => $kos ? (new DetailFasilitasModel())->daftarKode($kos['kd_kos']) : [],
            'fotoKos'       => $kos ? $kosModel->fotoKos($kos['kd_kos']) : [],
        ]);
    }

    public function simpan()
    {
        $mitra = $this->mitraSaatIni();
        if ($mitra['status_kyc'] !== 'verified') {
            return redirect()->to('/mitra/kos')->with('warning', 'Akun mitra Anda belum terverifikasi KYC. Kos baru dapat dikelola setelah admin memverifikasi.');
        }

        $kosModel = new KosModel();
        $kdKos    = (string) $this->request->getPost('kd_kos');
        $lama     = $kdKos !== '' ? $kosModel->where('kd_kos', $kdKos)->where('kd_mitra', $mitra['kd_mitra'])->first() : null;
        if ($kdKos !== '' && ! $lama) {
            return redirect()->to('/mitra/kos')->with('error', 'Data kos tidak ditemukan.');
        }

        $rules = [
            'nm_kos'     => 'required|max_length[50]',
            'alamat_kos' => 'required',
            'kota'       => 'required|max_length[30]',
            'tipe_kos'   => 'required|in_list[putra,putri,campur]',
            'status_kos' => 'required|in_list[aktif,nonaktif]',
            'latitude'   => 'permit_empty|decimal',
            'longitude'  => 'permit_empty|decimal',
        ];
        if (! $lama && $this->request->getPost('nm_tipe')) {
            $rules += [
                'nm_tipe'       => 'max_length[30]',
                'jml_kamar'     => 'required|is_natural_no_zero',
                'harga_bulanan' => 'required|is_natural',
                'harga_harian'  => 'permit_empty|is_natural',
                'harga_tahunan' => 'permit_empty|is_natural',
            ];
        }
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nm_kos'     => $this->request->getPost('nm_kos'),
            'alamat_kos' => $this->request->getPost('alamat_kos'),
            'kota'       => $this->request->getPost('kota'),
            'latitude'   => $this->request->getPost('latitude') ?: null,
            'longitude'  => $this->request->getPost('longitude') ?: null,
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'aturan_kos' => $this->request->getPost('aturan_kos'),
            'tipe_kos'   => $this->request->getPost('tipe_kos'),
            'status_kos' => $this->request->getPost('status_kos'),
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        if ($lama) {
            $kosModel->update($kdKos, $data);
        } else {
            $kdKos = kode('KOS-', 5);
            $kosModel->insert(['kd_kos' => $kdKos, 'kd_mitra' => $mitra['kd_mitra']] + $data);

            // Sequence Diagram Hal. 29: tipe kamar awal ikut disimpan bersama data kos
            if ($this->request->getPost('nm_tipe')) {
                $jml = (int) $this->request->getPost('jml_kamar');
                (new TipeKamarModel())->insert([
                    'kd_tipe'        => kode('TP-', 5),
                    'kd_kos'         => $kdKos,
                    'nm_tipe'        => $this->request->getPost('nm_tipe'),
                    'harga_harian'   => (int) $this->request->getPost('harga_harian'),
                    'harga_bulanan'  => (int) $this->request->getPost('harga_bulanan'),
                    'harga_tahunan'  => (int) $this->request->getPost('harga_tahunan'),
                    'jml_kamar'      => $jml,
                    'kamar_tersedia' => $jml,
                    'status_tipe'    => 'tersedia',
                ]);
            }
        }

        // Fasilitas
        $kodeFas = array_values(array_filter((array) $this->request->getPost('fasilitas'), 'is_string'));
        (new DetailFasilitasModel())->sinkron($kdKos, $kodeFas);

        // Foto kos (multiple upload)
        $fotoModel = new FotoKosModel();
        $urutan    = (int) $db->table('foto_kos')->selectMax('urutan')->where('kd_kos', $kdKos)->get()->getRow('urutan');
        $berkas    = $this->request->getFileMultiple('foto') ?: [];
        $gagal     = 0;
        foreach ($berkas as $f) {
            if ($f->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if (! $f->isValid() || strpos((string) $f->getMimeType(), 'image/') !== 0 || $f->getSize() > 3 * 1024 * 1024) {
                $gagal++;
                continue;
            }
            $nama = $this->simpanGambar($f, 'kos');
            if ($nama) {
                $fotoModel->insert(['kd_foto' => kode('FT-', 6), 'kd_kos' => $kdKos, 'url_foto' => $nama, 'urutan' => ++$urutan]);
            }
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Data kos gagal disimpan.');
        }

        $pesan = 'Data kos berhasil disimpan.' . ($gagal ? " ({$gagal} foto dilewati: harus gambar maks. 3 MB)" : '');
        if (! $lama) {
            $pesan .= ' Atur tipe kamar & harga di menu Kelola Kamar bila perlu.';
        }
        return redirect()->to('/mitra/kos')->with('success', $pesan);
    }

    public function hapus($kdKos)
    {
        $mitra = $this->mitraSaatIni();
        $kos   = (new KosModel())->where('kd_kos', $kdKos)->where('kd_mitra', $mitra['kd_mitra'])->first();
        if (! $kos) {
            return redirect()->to('/mitra/kos')->with('error', 'Data kos tidak ditemukan.');
        }

        $db   = \Config\Database::connect();
        $ada  = $db->table('pemesanan')->join('tipe_kamar', 'tipe_kamar.kd_tipe = pemesanan.kd_tipe')->where('tipe_kamar.kd_kos', $kdKos)->countAllResults();
        if ($ada > 0) {
            return redirect()->to('/mitra/kos')->with('warning', 'Kos sudah memiliki riwayat pesanan, tidak dapat dihapus. Ubah statusnya menjadi nonaktif.');
        }

        foreach ((new KosModel())->fotoKos($kdKos) as $f) {
            @unlink(FCPATH . 'uploads/kos/' . $f['url_foto']);
        }
        (new KosModel())->delete($kdKos);
        return redirect()->to('/mitra/kos')->with('success', 'Kos dihapus.');
    }

    public function hapusFoto($kdFoto)
    {
        $mitra = $this->mitraSaatIni();
        $foto  = \Config\Database::connect()->table('foto_kos')
            ->select('foto_kos.*')->join('kos', 'kos.kd_kos = foto_kos.kd_kos')
            ->where('foto_kos.kd_foto', $kdFoto)->where('kos.kd_mitra', $mitra['kd_mitra'])->get()->getRowArray();
        if (! $foto) {
            return redirect()->to('/mitra/kos')->with('error', 'Foto tidak ditemukan.');
        }
        @unlink(FCPATH . 'uploads/kos/' . $foto['url_foto']);
        (new FotoKosModel())->delete($kdFoto);
        return redirect()->to('/mitra/kos/edit/' . rawurlencode($foto['kd_kos']))->with('success', 'Foto dihapus.');
    }
}
