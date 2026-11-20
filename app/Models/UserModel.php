<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'user';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['username', 'nama_user', 'email', 'password', 'no_telp', 'alamat_user', 'role', 'status_user'];
    protected $useTimestamps = true;
}
