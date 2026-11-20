<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Data contoh agar alur UML bisa langsung dicoba.
 *   php spark db:seed AdminSeeder   (admin@kosqu.com / admin123)
 *   php spark db:seed DemoSeeder    (mitra@kosqu.com / mitra123, customer@kosqu.com / customer123)
 */
class DemoSeeder extends Seeder
{
    public function run()
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        // Fasilitas
        $fasilitas = [
            'FS-AC' => ['AC', '❄️'], 'FS-WIFI' => ['WiFi', '📶'], 'FS-KMD' => ['Kamar Mandi Dalam', '🚿'],
            'FS-KSR' => ['Kasur', '🛏️'], 'FS-LMR' => ['Lemari', '🗄️'], 'FS-MJA' => ['Meja & Kursi', '🪑'],
            'FS-DPR' => ['Dapur Bersama', '🍳'], 'FS-PKR' => ['Parkir Motor', '🛵'], 'FS-LDR' => ['Laundry', '🧺'], 'FS-CTV' => ['CCTV 24 Jam', '📹'],
        ];
        foreach ($fasilitas as $kd => [$nama, $ikon]) {
            if (! $db->table('fasilitas')->where('kd_fas', $kd)->countAllResults()) {
                $db->table('fasilitas')->insert(['kd_fas' => $kd, 'nm_fas' => $nama, 'ikon_fas' => $ikon]);
            }
        }

        // Akun demo
        $akun = static fn (string $u, string $nama, string $email, string $pass, string $role, string $telp) => [
            'username' => $u, 'nama_user' => $nama, 'email' => $email, 'password' => password_hash($pass, PASSWORD_DEFAULT),
            'no_telp' => $telp, 'alamat_user' => 'Bandung', 'role' => $role, 'status_user' => 'aktif', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($db->table('user')->where('email', 'mitra@kosqu.com')->countAllResults()) {
            echo "Data demo sudah ada, dilewati.\n";
            return;
        }
        $db->table('user')->insert($akun('mitra_demo', 'Bu Rahma (Mitra Demo)', 'mitra@kosqu.com', 'mitra123', 'mitra', '081300000001'));
        $idMitra = (int) $db->insertID();
        $db->table('user')->insert($akun('customer_demo', 'Andi Customer', 'customer@kosqu.com', 'customer123', 'customer', '081300000002'));

        $db->table('mitra')->insert([
            'kd_mitra' => 'MTR-DEMO', 'id' => $idMitra, 'no_ktp' => '3273010101010001', 'foto_ktp' => null, 'status_kyc' => 'verified',
            'nama_bank' => 'BCA', 'no_rekening' => '1234567890', 'atas_nama' => 'Rahma Demo', 'saldo' => 0,
        ]);

        // [kd_kos, nama, alamat, kota, lat, lng, tipe, tipe kamar [nama, harian, bulanan, tahunan, jumlah, tersedia], fasilitas]
        $kos = [
            ['KOS-D01', 'Kost Dipatiukur Bandung', 'Jl. Dipati Ukur No. 12, Lebakgede', 'Bandung', '-6.8901', '107.6150', 'putri', ['Tipe A (AC)', 90000, 1800000, 20000000, 10, 3], ['FS-AC', 'FS-WIFI', 'FS-KMD', 'FS-KSR', 'FS-LMR']],
            ['KOS-D02', 'Aranda Dago Residence', 'Jl. Ir. H. Juanda (Dago) No. 88', 'Bandung', '-6.8858', '107.6131', 'campur', ['Standar', 70000, 1668000, 18000000, 8, 2], ['FS-WIFI', 'FS-KSR', 'FS-MJA', 'FS-PKR', 'FS-CTV']],
            ['KOS-D03', 'Graha Teduh 99 Dago', 'Jl. Dago Asri No. 99', 'Bandung', '-6.8830', '107.6170', 'putra', ['Deluxe', 110000, 2400000, 26000000, 6, 1], ['FS-AC', 'FS-WIFI', 'FS-KMD', 'FS-LDR', 'FS-PKR']],
            ['KOS-D04', 'Silva Putri Cibeunying', 'Jl. Cibeunying Kidul No. 5', 'Bandung', '-6.9040', '107.6300', 'putri', ['Tipe B', 60000, 1450000, 16000000, 12, 5], ['FS-WIFI', 'FS-KMD', 'FS-DPR', 'FS-CTV']],
            ['KOS-D05', 'Kost Sukajadi Nyaman', 'Jl. Sukajadi No. 150', 'Bandung', '-6.8850', '107.5950', 'campur', ['Ekonomis', 50000, 885000, 9500000, 15, 9], ['FS-WIFI', 'FS-KSR', 'FS-LMR', 'FS-PKR']],
            ['KOS-D06', 'Kost Pajajaran Elite', 'Jl. Pajajaran No. 30', 'Bandung', '-6.9065', '107.5880', 'putra', ['Studio', 120000, 2450000, 27000000, 5, 0], ['FS-AC', 'FS-WIFI', 'FS-KMD', 'FS-KSR', 'FS-MJA', 'FS-LDR']],
        ];
        foreach ($kos as $i => [$kd, $nama, $alamat, $kota, $lat, $lng, $tipe, $kamar, $fas]) {
            $db->table('kos')->insert([
                'kd_kos' => $kd, 'kd_mitra' => 'MTR-DEMO', 'nm_kos' => $nama, 'alamat_kos' => $alamat, 'kota' => $kota,
                'latitude' => $lat, 'longitude' => $lng, 'tipe_kos' => $tipe, 'status_kos' => 'aktif',
                'deskripsi' => "Hunian nyaman dan strategis dekat kampus. Lingkungan tenang, akses mudah ke transportasi umum.",
                'aturan_kos' => "Jam malam 22.00 WIB.\nTamu lawan jenis dilarang masuk kamar.\nDilarang merokok di dalam kamar.",
            ]);
            [$nmTipe, $harian, $bulanan, $tahunan, $jml, $tersedia] = $kamar;
            $db->table('tipe_kamar')->insert([
                'kd_tipe' => 'TP-D0' . ($i + 1), 'kd_kos' => $kd, 'nm_tipe' => $nmTipe, 'harga_harian' => $harian, 'harga_bulanan' => $bulanan,
                'harga_tahunan' => $tahunan, 'jml_kamar' => $jml, 'kamar_tersedia' => $tersedia, 'status_tipe' => $tersedia > 0 ? 'tersedia' : 'penuh',
            ]);
            foreach ($fas as $f) {
                $db->table('detail_fasilitas')->insert(['kd_kos' => $kd, 'kd_fas' => $f]);
            }
        }
        echo "Data demo dibuat. Mitra: mitra@kosqu.com / mitra123 | Customer: customer@kosqu.com / customer123\n";
    }
}
