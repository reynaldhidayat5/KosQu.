<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Tabel pengaturan platform (use case Admin: "Mengatur komisi platform"). */
class CreatePengaturan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'kunci' => ['type' => 'VARCHAR', 'constraint' => 30],
            'nilai' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->addKey('kunci', true);
        $this->forge->createTable('pengaturan', true);

        $this->db->table('pengaturan')->insertBatch([
            ['kunci' => 'komisi_persen',     'nilai' => '5'],
            ['kunci' => 'biaya_layanan',     'nilai' => '15000'],
            ['kunci' => 'rekening_platform', 'nilai' => 'BCA 1234567890 a.n. KosQu Indonesia'],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan', true);
    }
}
