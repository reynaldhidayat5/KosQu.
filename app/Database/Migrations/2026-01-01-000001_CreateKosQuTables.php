<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKosQuTables extends Migration
{
    public function up()
    {
        // 1. Tabel User
        $this->forge->addField([
            'id'          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'username'    => ['type' => 'VARCHAR', 'constraint' => 25, 'unique' => true],
            'nama_user'   => ['type' => 'VARCHAR', 'constraint' => 40],
            'email'       => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'password'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'no_telp'     => ['type' => 'VARCHAR', 'constraint' => 15],
            'alamat_user' => ['type' => 'TEXT', 'null' => true],
            'role'        => ['type' => 'ENUM', 'constraint' => ['customer', 'mitra', 'admin'], 'default' => 'customer'],
            'status_user' => ['type' => 'ENUM', 'constraint' => ['aktif', 'nonaktif'], 'default' => 'aktif'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user');

        // 2. Tabel Mitra
        $this->forge->addField([
            'kd_mitra'    => ['type' => 'VARCHAR', 'constraint' => 10],
            'id'          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'no_ktp'      => ['type' => 'VARCHAR', 'constraint' => 16],
            'foto_ktp'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'status_kyc'  => ['type' => 'ENUM', 'constraint' => ['pending', 'verified', 'rejected'], 'default' => 'pending'],
            'nama_bank'   => ['type' => 'VARCHAR', 'constraint' => 30],
            'no_rekening' => ['type' => 'VARCHAR', 'constraint' => 25],
            'atas_nama'   => ['type' => 'VARCHAR', 'constraint' => 40],
            'saldo'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('kd_mitra', true);
        $this->forge->addForeignKey('id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mitra');

        // 3. Tabel Fasilitas
        $this->forge->addField([
            'kd_fas'   => ['type' => 'VARCHAR', 'constraint' => 10],
            'nm_fas'   => ['type' => 'VARCHAR', 'constraint' => 30],
            'ikon_fas' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);
        $this->forge->addKey('kd_fas', true);
        $this->forge->createTable('fasilitas');

        // 4. Tabel Kos
        $this->forge->addField([
            'kd_kos'     => ['type' => 'VARCHAR', 'constraint' => 10],
            'kd_mitra'   => ['type' => 'VARCHAR', 'constraint' => 10],
            'nm_kos'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'alamat_kos' => ['type' => 'TEXT'],
            'kota'       => ['type' => 'VARCHAR', 'constraint' => 30],
            'latitude'   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'longitude'  => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'deskripsi'  => ['type' => 'TEXT', 'null' => true],
            'aturan_kos' => ['type' => 'TEXT', 'null' => true],
            'tipe_kos'   => ['type' => 'ENUM', 'constraint' => ['putra', 'putri', 'campur']],
            'status_kos' => ['type' => 'ENUM', 'constraint' => ['aktif', 'nonaktif'], 'default' => 'aktif'],
        ]);
        $this->forge->addKey('kd_kos', true);
        $this->forge->addForeignKey('kd_mitra', 'mitra', 'kd_mitra', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kos');

        // 5. Tabel Detail Fasilitas (Pivot)
        $this->forge->addField([
            'kd_kos' => ['type' => 'VARCHAR', 'constraint' => 10],
            'kd_fas' => ['type' => 'VARCHAR', 'constraint' => 10],
        ]);
        $this->forge->addKey(['kd_kos', 'kd_fas'], true);
        $this->forge->addForeignKey('kd_kos', 'kos', 'kd_kos', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kd_fas', 'fasilitas', 'kd_fas', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_fasilitas');

        // 6. Tabel Foto Kos
        $this->forge->addField([
            'kd_foto'  => ['type' => 'VARCHAR', 'constraint' => 10],
            'kd_kos'   => ['type' => 'VARCHAR', 'constraint' => 10],
            'url_foto' => ['type' => 'VARCHAR', 'constraint' => 100],
            'urutan'   => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
        ]);
        $this->forge->addKey('kd_foto', true);
        $this->forge->addForeignKey('kd_kos', 'kos', 'kd_kos', 'CASCADE', 'CASCADE');
        $this->forge->createTable('foto_kos');

        // 7. Tabel Tipe Kamar
        $this->forge->addField([
            'kd_tipe'        => ['type' => 'VARCHAR', 'constraint' => 10],
            'kd_kos'         => ['type' => 'VARCHAR', 'constraint' => 10],
            'nm_tipe'        => ['type' => 'VARCHAR', 'constraint' => 30],
            'harga_harian'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'harga_bulanan'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'harga_tahunan'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'jml_kamar'      => ['type' => 'INT', 'constraint' => 11],
            'kamar_tersedia' => ['type' => 'INT', 'constraint' => 11],
            'status_tipe'    => ['type' => 'ENUM', 'constraint' => ['tersedia', 'penuh'], 'default' => 'tersedia'],
        ]);
        $this->forge->addKey('kd_tipe', true);
        $this->forge->addForeignKey('kd_kos', 'kos', 'kd_kos', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tipe_kamar');

        // 8. Tabel Pemesanan
        $this->forge->addField([
            'no_pesan'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'kd_tipe'       => ['type' => 'VARCHAR', 'constraint' => 10],
            'tgl_mulai'     => ['type' => 'DATE'],
            'durasi'        => ['type' => 'INT', 'constraint' => 11],
            'satuan_durasi' => ['type' => 'ENUM', 'constraint' => ['harian', 'bulanan', 'tahunan']],
            'sub_total'     => ['type' => 'INT', 'constraint' => 11],
            'biaya_layanan' => ['type' => 'INT', 'constraint' => 11],
            'total_bayar'   => ['type' => 'INT', 'constraint' => 11],
            'batas_bayar'   => ['type' => 'DATETIME'],
            'status_pesan'  => ['type' => 'ENUM', 'constraint' => ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Berhasil', 'Ditolak', 'Dibatalkan'], 'default' => 'Menunggu Pembayaran'],
        ]);
        $this->forge->addKey('no_pesan', true);
        $this->forge->addForeignKey('id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kd_tipe', 'tipe_kamar', 'kd_tipe', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pemesanan');

        // 9. Tabel Pembayaran
        $this->forge->addField([
            'no_bayar'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'no_pesan'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'metode_bayar'   => ['type' => 'ENUM', 'constraint' => ['transfer_manual', 'va', 'ewallet']],
            'tgl_bayar'      => ['type' => 'DATE'],
            'jumlah_bayar'   => ['type' => 'INT', 'constraint' => 11],
            'bukti_transfer' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'ref_gateway'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'status_bayar'   => ['type' => 'ENUM', 'constraint' => ['pending', 'settlement', 'rejected'], 'default' => 'pending'],
        ]);
        $this->forge->addKey('no_bayar', true);
        $this->forge->addForeignKey('no_pesan', 'pemesanan', 'no_pesan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pembayaran');

        // 10. Tabel Invoice
        $this->forge->addField([
            'no_invoice'    => ['type' => 'VARCHAR', 'constraint' => 20],
            'no_pesan'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'tgl_terbit'    => ['type' => 'DATE'],
            'total_tagihan' => ['type' => 'INT', 'constraint' => 11],
            'file_pdf'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ]);
        $this->forge->addKey('no_invoice', true);
        $this->forge->addForeignKey('no_pesan', 'pemesanan', 'no_pesan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invoice');

        // 11. Tabel Review
        $this->forge->addField([
            'kd_review'  => ['type' => 'VARCHAR', 'constraint' => 10],
            'no_pesan'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'rating'     => ['type' => 'INT', 'constraint' => 1],
            'komentar'   => ['type' => 'TEXT'],
            'tgl_review' => ['type' => 'DATE'],
        ]);
        $this->forge->addKey('kd_review', true);
        $this->forge->addForeignKey('no_pesan', 'pemesanan', 'no_pesan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('review');

        // 12. Tabel Penarikan Dana
        $this->forge->addField([
            'kd_tarik'      => ['type' => 'VARCHAR', 'constraint' => 10],
            'kd_mitra'      => ['type' => 'VARCHAR', 'constraint' => 10],
            'id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true], // Admin pemroses
            'jumlah_tarik'  => ['type' => 'INT', 'constraint' => 11],
            'komisi'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'jumlah_bersih' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status_tarik'  => ['type' => 'ENUM', 'constraint' => ['Diajukan', 'Dicairkan', 'Ditolak'], 'default' => 'Diajukan'],
            'tgl_ajuan'     => ['type' => 'DATE'],
            'tgl_proses'    => ['type' => 'DATE', 'null' => true],
        ]);
        $this->forge->addKey('kd_tarik', true);
        $this->forge->addForeignKey('kd_mitra', 'mitra', 'kd_mitra', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penarikan_dana');
    }

    public function down()
    {
        $this->forge->dropTable('penarikan_dana', true);
        $this->forge->dropTable('review', true);
        $this->forge->dropTable('invoice', true);
        $this->forge->dropTable('pembayaran', true);
        $this->forge->dropTable('pemesanan', true);
        $this->forge->dropTable('tipe_kamar', true);
        $this->forge->dropTable('foto_kos', true);
        $this->forge->dropTable('detail_fasilitas', true);
        $this->forge->dropTable('kos', true);
        $this->forge->dropTable('fasilitas', true);
        $this->forge->dropTable('mitra', true);
        $this->forge->dropTable('user', true);
    }
}