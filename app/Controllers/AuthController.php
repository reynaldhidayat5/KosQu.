<?php

namespace App\Controllers;

use App\Models\MitraModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    // Activity Diagram Hal. 4 & Sequence Diagram Hal. 23: Login
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to($this->halamanUtama((string) session()->get('role')));
        }
        // ?next=/path -> setelah login kembali ke halaman tujuan (mis. alur "Pesan Sekarang")
        $next = $this->request->getGet('next');
        if ($next) {
            session()->set('redirect_after_login', aman_redirect($next));
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $user = (new UserModel())->where('email', $email)->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah!');
        }
        if ($user['status_user'] !== 'aktif') {
            return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Hubungi admin.');
        }

        $tujuan = session()->get('redirect_after_login');
        session()->regenerate();

        $data = [
            'id'         => (int) $user['id'],
            'nama_user'  => $user['nama_user'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'isLoggedIn' => true,
        ];
        if ($user['role'] === 'mitra') {
            $mitra = (new MitraModel())->where('id', $user['id'])->first();
            $data['kd_mitra']   = $mitra['kd_mitra'] ?? null;
            $data['status_kyc'] = $mitra['status_kyc'] ?? 'pending';
        }
        session()->set($data);
        session()->remove('redirect_after_login');

        // Dashboard sesuai role. Customer kembali ke halaman tujuan sebelumnya (jika ada).
        if ($user['role'] === 'customer' && $tujuan) {
            return redirect()->to(aman_redirect($tujuan));
        }
        return redirect()->to($this->halamanUtama($user['role']));
    }

    private function halamanUtama(string $role): string
    {
        return match ($role) {
            'admin' => '/admin/dashboard',
            'mitra' => '/mitra/dashboard',
            default => '/',
        };
    }

    // Registrasi akun (Customer) & registrasi mitra
    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to($this->halamanUtama((string) session()->get('role')));
        }
        return view('auth/register');
    }

    public function storeRegister()
    {
        $role = (string) $this->request->getPost('role');
        if (! in_array($role, ['customer', 'mitra'], true)) {
            $role = 'customer'; // admin tidak boleh dibuat lewat form publik
        }

        $rules = [
            'nama_user' => 'required|max_length[40]',
            'username'  => 'required|alpha_dash|max_length[25]|is_unique[user.username]',
            'email'     => 'required|valid_email|max_length[50]|is_unique[user.email]',
            'no_telp'   => 'required|max_length[15]|regex_match[/^[0-9+]+$/]',
            'password'  => 'required|min_length[6]|max_length[72]',
        ];
        if ($role === 'mitra') {
            $rules += [
                'no_ktp'      => 'required|exact_length[16]|numeric',
                'nama_bank'   => 'required|max_length[30]',
                'no_rekening' => 'required|max_length[25]|numeric',
                'atas_nama'   => 'required|max_length[40]',
                'foto_ktp'    => 'uploaded[foto_ktp]|is_image[foto_ktp]|max_size[foto_ktp,3072]',
            ];
        }
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $userId = (new UserModel())->insert([
            'username'    => $this->request->getPost('username'),
            'nama_user'   => $this->request->getPost('nama_user'),
            'email'       => $this->request->getPost('email'),
            'password'    => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'no_telp'     => $this->request->getPost('no_telp'),
            'alamat_user' => $this->request->getPost('alamat_user'),
            'role'        => $role,
            'status_user' => 'aktif',
        ]);

        if ($role === 'mitra') {
            $ktp = $this->simpanGambar($this->request->getFile('foto_ktp'), 'ktp');
            (new MitraModel())->insert([
                'kd_mitra'    => kode('MTR-', 5),
                'id'          => $userId,
                'no_ktp'      => $this->request->getPost('no_ktp'),
                'foto_ktp'    => $ktp,
                'status_kyc'  => 'pending',
                'nama_bank'   => $this->request->getPost('nama_bank'),
                'no_rekening' => $this->request->getPost('no_rekening'),
                'atas_nama'   => $this->request->getPost('atas_nama'),
                'saldo'       => 0,
            ]);
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Registrasi gagal disimpan. Coba lagi.');
        }

        $pesan = $role === 'mitra'
            ? 'Registrasi mitra berhasil! Akun Anda menunggu verifikasi KYC oleh admin. Silakan login.'
            : 'Registrasi berhasil! Silakan login.';
        return redirect()->to('/login')->with('success', $pesan);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
