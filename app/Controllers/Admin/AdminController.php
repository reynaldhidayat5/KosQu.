<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FasilitasModel;
use App\Models\KosModel;
use App\Models\MitraModel;
use App\Models\PemesananModel;
use App\Models\PengaturanModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $db = \Config\Database::connect();

        $data['stats'] = [
            'total_user'    => $db->table('user')->countAllResults(),
            'total_kos'     => $db->table('kos')->countAllResults(),
            'pesanan_aktif' => $db->table('pemesanan')->where('status_pesan', 'Berhasil')->countAllResults(),
            'kyc_pending'   => $db->table('mitra')->where('status_kyc', 'pending')->countAllResults(),
            'payout_baru'   => $db->table('penarikan_dana')->where('status_tarik', 'Diajukan')->countAllResults(),
            'transaksi'     => (int) ($db->table('pemesanan')->selectSum('total_bayar')->where('status_pesan', 'Berhasil')->get()->getRow()->total_bayar ?? 0),
        ];

        return view('admin/dashboard', $data);
    }

    // ---------------- Master data ----------------
    public function user()
    {
        $cari  = trim((string) $this->request->getGet('cari'));
        $model = (new UserModel())->orderBy('role', 'ASC')->orderBy('nama_user', 'ASC');
        if ($cari !== '') {
            $model->groupStart()->like('nama_user', $cari)->orLike('email', $cari)->orLike('username', $cari)->groupEnd();
        }
        return view('admin/users', ['users' => $model->findAll(), 'cari' => $cari]);
    }

    public function statusUser(int|string $id)
    {
        $model = new UserModel();
        $user  = $model->find($id);
        if (! $user) {
            return redirect()->to('/admin/user')->with('error', 'User tidak ditemukan.');
        }
        if ((int) $user['id'] === (int) session()->get('id')) {
            return redirect()->to('/admin/user')->with('warning', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }
        $baru = $user['status_user'] === 'aktif' ? 'nonaktif' : 'aktif';
        $model->update($id, ['status_user' => $baru]);
        return redirect()->to('/admin/user')->with('success', "Akun {$user['nama_user']} sekarang {$baru}.");
    }

    public function mitra()
    {
        $daftar = (new MitraModel())->select('mitra.*, user.nama_user, user.email, user.no_telp, (SELECT COUNT(*) FROM kos WHERE kos.kd_mitra = mitra.kd_mitra) AS jml_kos', false)
            ->join('user', 'user.id = mitra.id')->orderBy('user.nama_user', 'ASC')->findAll();
        return view('admin/mitra', ['daftar' => $daftar]);
    }

    public function kos()
    {
        $daftar = (new KosModel())->select('kos.*, user.nama_user AS nama_pemilik, mitra.status_kyc', false)
            ->join('mitra', 'mitra.kd_mitra = kos.kd_mitra')->join('user', 'user.id = mitra.id')
            ->orderBy('kos.nm_kos', 'ASC')->findAll();
        return view('admin/kos', ['daftar' => $daftar]);
    }

    public function statusKos(string $kdKos)
    {
        $model = new KosModel();
        $kos   = $model->find($kdKos);
        if (! $kos) {
            return redirect()->to('/admin/kos')->with('error', 'Kos tidak ditemukan.');
        }
        $baru = $kos['status_kos'] === 'aktif' ? 'nonaktif' : 'aktif';
        $model->update($kdKos, ['status_kos' => $baru]);
        return redirect()->to('/admin/kos')->with('success', "Kos {$kos['nm_kos']} sekarang {$baru}.");
    }

    public function fasilitas(?string $kdFas = null)
    {
        $model = new FasilitasModel();
        return view('admin/fasilitas', [
            'daftar' => $model->orderBy('nm_fas', 'ASC')->findAll(),
            'edit'   => $kdFas ? $model->find($kdFas) : null,
        ]);
    }

    public function simpanFasilitas()
    {
        if (! $this->validate(['nm_fas' => 'required|max_length[30]', 'ikon_fas' => 'permit_empty|max_length[50]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $model = new FasilitasModel();
        $data  = ['nm_fas' => $this->request->getPost('nm_fas'), 'ikon_fas' => $this->request->getPost('ikon_fas')];
        $kd    = (string) $this->request->getPost('kd_fas');
        if ($kd !== '' && $model->find($kd)) {
            $model->update($kd, $data);
        } else {
            $model->insert(['kd_fas' => kode('FS-', 5)] + $data);
        }
        return redirect()->to('/admin/fasilitas')->with('success', 'Fasilitas disimpan.');
    }

    public function hapusFasilitas(string $kdFas)
    {
        (new FasilitasModel())->delete($kdFas);
        return redirect()->to('/admin/fasilitas')->with('success', 'Fasilitas dihapus.');
    }

    // ---------------- Verifikasi mitra (KYC) ----------------
    public function kyc()
    {
        $daftar = (new MitraModel())->select('mitra.*, user.nama_user, user.email, user.no_telp')
            ->join('user', 'user.id = mitra.id')
            ->orderBy("FIELD(mitra.status_kyc, 'pending', 'rejected', 'verified')", '', false)
            ->findAll();
        return view('admin/verifikasi_kyc', ['mitraList' => $daftar]);
    }

    public function verifikasiKyc(string $kdMitra, string $status)
    {
        if (! in_array($status, ['verified', 'rejected'], true)) {
            return redirect()->to('/admin/kyc')->with('error', 'Status tidak valid.');
        }
        $model = new MitraModel();
        if (! $model->find($kdMitra)) {
            return redirect()->to('/admin/kyc')->with('error', 'Mitra tidak ditemukan.');
        }
        $model->update($kdMitra, ['status_kyc' => $status]);
        return redirect()->to('/admin/kyc')->with('success', 'Status KYC mitra diperbarui menjadi: ' . $status);
    }

    // ---------------- Transaksi & pembayaran ----------------
    public function transaksi()
    {
        $status = (string) $this->request->getGet('status');
        $model  = new PemesananModel();
        $model->batalkanKedaluwarsa();
        return view('admin/transaksi', ['daftar' => $model->semua($status ?: null), 'status' => $status]);
    }

    public function pembayaran()
    {
        $daftar = \Config\Database::connect()->table('pembayaran')
            ->select('pembayaran.*, pemesanan.status_pesan, user.nama_user')
            ->join('pemesanan', 'pemesanan.no_pesan = pembayaran.no_pesan')
            ->join('user', 'user.id = pemesanan.id')
            ->orderBy('pembayaran.tgl_bayar', 'DESC')->orderBy('pembayaran.no_bayar', 'DESC')
            ->get()->getResultArray();
        return view('admin/pembayaran', ['daftar' => $daftar]);
    }

    // ---------------- Komisi platform ----------------
    public function pengaturan()
    {
        return view('admin/pengaturan', ['p' => (new PengaturanModel())->semua()]);
    }

    public function simpanPengaturan()
    {
        $rules = [
            'komisi_persen'     => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[50]',
            'biaya_layanan'     => 'required|is_natural',
            'rekening_platform' => 'required|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $model = new PengaturanModel();
        foreach (['komisi_persen', 'biaya_layanan', 'rekening_platform'] as $k) {
            $model->simpan($k, trim((string) $this->request->getPost($k)));
        }
        return redirect()->to('/admin/pengaturan')->with('success', 'Pengaturan platform disimpan.');
    }

    // ---------------- Laporan transaksi global ----------------
    public function laporan()
    {
        $dari   = (string) $this->request->getGet('dari');
        $sampai = (string) $this->request->getGet('sampai');
        $db     = \Config\Database::connect();

        $b = $db->table('invoice')
            ->select('invoice.no_invoice, invoice.tgl_terbit, pemesanan.no_pesan, pemesanan.sub_total, pemesanan.biaya_layanan, pemesanan.total_bayar, kos.nm_kos, user.nama_user')
            ->join('pemesanan', 'pemesanan.no_pesan = invoice.no_pesan')
            ->join('tipe_kamar', 'tipe_kamar.kd_tipe = pemesanan.kd_tipe')
            ->join('kos', 'kos.kd_kos = tipe_kamar.kd_kos')
            ->join('user', 'user.id = pemesanan.id');
        if ($dari !== '') {
            $b->where('invoice.tgl_terbit >=', $dari);
        }
        if ($sampai !== '') {
            $b->where('invoice.tgl_terbit <=', $sampai);
        }
        $transaksi = $b->orderBy('invoice.tgl_terbit', 'DESC')->get()->getResultArray();

        $komisi = (int) ($db->table('penarikan_dana')->selectSum('komisi')->where('status_tarik', 'Dicairkan')->get()->getRow('komisi') ?? 0);

        $laporan = [
            'total_user'      => $db->table('user')->countAllResults(),
            'total_mitra'     => $db->table('mitra')->countAllResults(),
            'total_kos'       => $db->table('kos')->countAllResults(),
            'total_pesanan'   => count($transaksi),
            'total_transaksi' => array_sum(array_column($transaksi, 'total_bayar')),
            'biaya_layanan'   => array_sum(array_column($transaksi, 'biaya_layanan')),
            'komisi'          => $komisi,
        ];
        return view('admin/laporan_global', ['laporan' => $laporan, 'transaksi' => $transaksi, 'dari' => $dari, 'sampai' => $sampai]);
    }
}