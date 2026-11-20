<?php
namespace App\Models;
use CodeIgniter\Model;

class PenarikanDanaModel extends Model
{
    protected $table            = 'penarikan_dana';
    protected $primaryKey       = 'kd_tarik';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['kd_tarik', 'kd_mitra', 'id', 'jumlah_tarik', 'komisi', 'jumlah_bersih', 'status_tarik', 'tgl_ajuan', 'tgl_proses'];
}