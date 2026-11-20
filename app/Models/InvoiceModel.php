<?php
namespace App\Models;
use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoice';
    protected $primaryKey       = 'no_invoice';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['no_invoice', 'no_pesan', 'tgl_terbit', 'total_tagihan', 'file_pdf'];
}