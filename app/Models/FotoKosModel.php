<?php

namespace App\Models;

use CodeIgniter\Model;

class FotoKosModel extends Model
{
    protected $table            = 'foto_kos';
    protected $primaryKey       = 'kd_foto';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['kd_foto', 'kd_kos', 'url_foto', 'urutan'];
}
