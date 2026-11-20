<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'username'    => 'admin',
            'nama_user'   => 'Administrator KosQu',
            'email'       => 'admin@kosqu.com',
            'password'    => password_hash('admin123', PASSWORD_DEFAULT),
            'no_telp'     => '081234567890',
            'alamat_user' => 'Kantor Pusat KosQu',
            'role'        => 'admin',
            'status_user' => 'aktif',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('user')->insert($data);
    }
}