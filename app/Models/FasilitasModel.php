<?php
namespace App\Models;
use CodeIgniter\Model;

class FasilitasModel extends Model
{
    protected $table            = 'fasilitas';
    protected $primaryKey       = 'kd_fas';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['kd_fas', 'nm_fas', 'ikon_fas'];
}