<?php
/**
 * @var array{
 *     no_invoice: string,
 *     tgl_terbit: string,
 *     total_tagihan: int|float
 * } $invoice
 * @var array{
 *     nama_user: string,
 *     email: string,
 *     no_telp: string,
 *     nm_kos: string,
 *     alamat_kos: string,
 *     kota: string,
 *     nm_tipe: string,
 *     durasi: int|string,
 *     satuan_durasi: string,
 *     tgl_mulai: string,
 *     sub_total: int|float,
 *     biaya_layanan: int|float,
 *     no_pesan: string,
 *     metode_bayar: ?string
 * } $p
 */
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Kuitansi <?= esc($invoice['no_invoice']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/kosqu.css') ?>" rel="stylesheet">
</head>
<body style="background:#f4f2ea">
<div class="container py-4" style="max-width:760px">
    <div class="d-flex justify-content-between mb-3 no-print">
        <a href="<?= site_url('/customer/riwayat') ?>" class="btn btn-outline-secondary btn-sm">&larr; Kembali</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>
    <div class="kq-card p-4 p-md-5" style="background:#fff">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <div class="kq-brand" style="font-size:1.5rem">KosQu</div>
                <div class="small text-secondary">E-Kuitansi Pemesanan Kos</div>
            </div>
            <div class="text-end">
                <div class="fw-bold"><?= esc($invoice['no_invoice']) ?></div>
                <div class="small text-secondary">Terbit <?= esc(date('d M Y', strtotime($invoice['tgl_terbit']))) ?></div>
                <span class="kq-badge ok mt-1">LUNAS</span>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-6">
                <div class="small text-secondary">Penyewa</div>
                <b><?= esc($p['nama_user']) ?></b>
                <div class="small"><?= esc($p['email']) ?><br><?= esc($p['no_telp']) ?></div>
            </div>
            <div class="col-6">
                <div class="small text-secondary">Properti</div>
                <b><?= esc($p['nm_kos']) ?></b>
                <div class="small"><?= esc($p['alamat_kos']) ?>, <?= esc($p['kota']) ?></div>
            </div>
        </div>
        <table class="kq-table mb-3">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sewa <?= esc($p['nm_tipe']) ?> — <?= (int) $p['durasi'] ?> <?= ['harian' => 'hari', 'bulanan' => 'bulan', 'tahunan' => 'tahun'][$p['satuan_durasi']] ?? '' ?>, mulai <?= esc(date('d M Y', strtotime($p['tgl_mulai']))) ?></td>
                    <td class="text-end"><?= rupiah($p['sub_total']) ?></td>
                </tr>
                <tr>
                    <td>Biaya layanan</td>
                    <td class="text-end"><?= rupiah($p['biaya_layanan']) ?></td>
                </tr>
                <tr>
                    <td class="fw-bold">Total dibayar</td>
                    <td class="text-end fw-bold"><?= rupiah($invoice['total_tagihan']) ?></td>
                </tr>
            </tbody>
        </table>
        <div class="small text-secondary">No. pesanan: <?= esc($p['no_pesan']) ?> · Metode: <?= esc(str_replace('_', ' ', (string) ($p['metode_bayar'] ?? '-'))) ?></div>
    </div>
</div>
</body>
</html>