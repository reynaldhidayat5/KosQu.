<?php

namespace App\Controllers;

use App\Models\MitraModel;
use App\Models\UserModel;

/** Menu akun: mengubah data profil (customer/mitra/admin) + data rekening (mitra). */
class AkunController extends BaseController
{
    public function index()
    {
        $user  = (new UserModel())->find(session()->get('id'));
        $mitra = session()->get('role') === 'mitra' ? $this->mitraSaatIni() : null;
        return view('akun/profil', ['user' => $user, 'mitra' => $mitra]);
    }

    public function simpan()
    {
        $id   = (int) session()->get('id');
        $role = session()->get('role');

        $rules = [
            'nama_user' => 'required|max_length[40]',
            'no_telp'   => 'required|max_length[15]|regex_match[/^[0-9+]+$/]',
            'password'  => 'permit_empty|min_length[6]|max_length[72]',
        ];
        if ($role === 'mitra') {
            $rules += [
                'nama_bank'   => 'required|max_length[30]',
                'no_rekening' => 'required|max_length[25]|numeric',
                'atas_nama'   => 'required|max_length[40]',
            ];
        }
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_user'   => $this->request->getPost('nama_user'),
            'no_telp'     => $this->request->getPost('no_telp'),
            'alamat_user' => $this->request->getPost('alamat_user'),
        ];
        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        (new UserModel())->update($id, $data);
        session()->set('nama_user', $data['nama_user']);

        if ($role === 'mitra' && ($mitra = $this->mitraSaatIni())) {
            (new MitraModel())->update($mitra['kd_mitra'], [
                'nama_bank'   => $this->request->getPost('nama_bank'),
                'no_rekening' => $this->request->getPost('no_rekening'),
                'atas_nama'   => $this->request->getPost('atas_nama'),
            ]);
        }
        return redirect()->to('/akun')->with('success', 'Profil berhasil diperbarui.');
    }
}
