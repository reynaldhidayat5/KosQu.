<?php
namespace App\Models;
use CodeIgniter\Model;

class MitraModel extends Model
{
    protected $table            = 'mitra';
    protected $primaryKey       = 'kd_mitra';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['kd_mitra', 'id', 'no_ktp', 'foto_ktp', 'status_kyc', 'nama_bank', 'no_rekening', 'atas_nama', 'saldo'];
}