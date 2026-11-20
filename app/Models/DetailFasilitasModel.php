<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailFasilitasModel extends Model
{
    protected $table            = 'detail_fasilitas';
    protected $primaryKey       = 'kd_kos';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['kd_kos', 'kd_fas'];

    /** Ganti seluruh fasilitas milik sebuah kos. */
    public function sinkron(string $kdKos, array $daftarFas): void
    {
        $this->db->table('detail_fasilitas')->where('kd_kos', $kdKos)->delete();
        foreach (array_unique($daftarFas) as $kdFas) {
            $this->db->table('detail_fasilitas')->insert(['kd_kos' => $kdKos, 'kd_fas' => $kdFas]);
        }
    }

    public function daftarKode(string $kdKos): array
    {
        return array_column($this->db->table('detail_fasilitas')->where('kd_kos', $kdKos)->get()->getResultArray(), 'kd_fas');
    }
}
