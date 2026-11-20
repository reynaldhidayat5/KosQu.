<?php
namespace App\Models;
use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table            = 'review';
    protected $primaryKey       = 'kd_review';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['kd_review', 'no_pesan', 'rating', 'komentar', 'tgl_review'];
}