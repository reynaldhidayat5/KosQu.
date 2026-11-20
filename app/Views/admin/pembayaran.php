<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Data Pembayaran<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Data pembayaran</h1><p class="kq-lead">Pantau pembayaran transfer manual dan Payment Gateway.</p>
<div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
    <thead><tr><th>No. bayar</th><th>Pesanan</th><th>Customer</th><th>Metode</th><th>Tanggal</th><th>Jumlah</th><th>Referensi / bukti</th><th>Status</th></tr></thead>
    <tbody><?php foreach ($daftar as $b): ?>
        <tr><td><?= esc($b['no_bayar']) ?></td><td><?= esc($b['no_pesan']) ?></td><td><?= esc($b['nama_user']) ?></td><td class="text-capitalize"><?= esc(str_replace('_', ' ', $b['metode_bayar'])) ?></td>
            <td><?= esc(date('d M Y', strtotime($b['tgl_bayar']))) ?></td><td><?= rupiah($b['jumlah_bayar']) ?></td>
            <td><?php if ($b['bukti_transfer']): ?><a target="_blank" href="<?= base_url('uploads/bukti/' . rawurlencode($b['bukti_transfer'])) ?>">Lihat bukti</a><?php else: ?><?= esc($b['ref_gateway'] ?? '—') ?><?php endif ?></td>
            <td><span class="kq-badge <?= badge_status($b['status_bayar']) ?>"><?= esc($b['status_bayar']) ?></span></td></tr>
    <?php endforeach ?><?php if (! $daftar): ?><tr><td colspan="8" class="text-center text-secondary py-4">Belum ada pembayaran.</td></tr><?php endif ?></tbody>
</table></div></div>
<?= $this->endSection() ?>
