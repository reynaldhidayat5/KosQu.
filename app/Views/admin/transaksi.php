<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Transaksi Global<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Transaksi global</h1><p class="kq-lead">Seluruh pemesanan di platform.</p>
<form method="get" class="d-flex gap-2 mb-3" style="max-width:380px" action="<?= site_url('/admin/transaksi') ?>">
    <select name="status" class="form-select"><option value="">Semua status</option><?php foreach (['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Berhasil', 'Ditolak', 'Dibatalkan'] as $s): ?><option <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach ?></select>
    <button class="btn btn-primary" type="submit">Filter</button></form>
<div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
    <thead><tr><th>No. pesanan</th><th>Customer</th><th>Kos</th><th>Mulai</th><th>Sub total</th><th>Total</th><th>Bayar</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($daftar as $r): ?>
        <tr><td class="fw-semibold"><?= esc($r['no_pesan']) ?></td><td><?= esc($r['nama_user']) ?></td><td><?= esc($r['nm_kos']) ?><div class="small text-secondary"><?= esc($r['nm_tipe']) ?></div></td><td><?= esc(date('d M Y', strtotime($r['tgl_mulai']))) ?></td>
            <td><?= rupiah($r['sub_total']) ?></td><td><?= rupiah($r['total_bayar']) ?></td>
            <td><?= $r['status_bayar'] ? '<span class="kq-badge ' . badge_status($r['status_bayar']) . '">' . esc($r['status_bayar']) . '</span>' : '<span class="text-secondary small">—</span>' ?></td>
            <td><span class="kq-badge <?= badge_status($r['status_pesan']) ?>"><?= esc($r['status_pesan']) ?></span></td></tr>
    <?php endforeach ?><?php if (! $daftar): ?><tr><td colspan="8" class="text-center text-secondary py-4">Tidak ada transaksi.</td></tr><?php endif ?></tbody>
</table></div></div>
<?= $this->endSection() ?>
