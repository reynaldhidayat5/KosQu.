<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Pengaturan platform (komisi, biaya layanan, rekening tujuan transfer manual).
 * Bila tabel `pengaturan` belum dimigrasi, nilai bawaan tetap dipakai.
 */
class PengaturanModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'kunci';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['kunci', 'nilai'];

    public const DEFAULTS = [
        'komisi_persen'     => '5',
        'biaya_layanan'     => '15000',
        'rekening_platform' => 'BCA 1234567890 a.n. KosQu Indonesia',
    ];

    private function tersedia(): bool
    {
        return $this->db->tableExists('pengaturan');
    }

    public function ambil(string $kunci): string
    {
        if ($this->tersedia()) {
            $row = $this->where('kunci', $kunci)->first();
            if ($row) {
                return (string) $row['nilai'];
            }
        }
        return self::DEFAULTS[$kunci] ?? '';
    }

    public function semua(): array
    {
        $hasil = self::DEFAULTS;
        if ($this->tersedia()) {
            foreach ($this->findAll() as $row) {
                $hasil[$row['kunci']] = $row['nilai'];
            }
        }
        return $hasil;
    }

    public function simpan(string $kunci, string $nilai): void
    {
        if (! $this->tersedia()) {
            return;
        }
        $ada = $this->where('kunci', $kunci)->first();
        if ($ada) {
            $this->db->table('pengaturan')->where('kunci', $kunci)->update(['nilai' => $nilai]);
        } else {
            $this->db->table('pengaturan')->insert(['kunci' => $kunci, 'nilai' => $nilai]);
        }
    }
}
