<?php
namespace App\Models;
use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'no_bayar';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['no_bayar', 'no_pesan', 'metode_bayar', 'tgl_bayar', 'jumlah_bayar', 'bukti_transfer', 'ref_gateway', 'status_bayar'];
}