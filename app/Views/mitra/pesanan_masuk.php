<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Pesanan Masuk<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Pesanan masuk</h1>
<p class="kq-lead">Periksa bukti pembayaran, lalu verifikasi &amp; terima atau tolak pesanan.</p>
<?php if ($pesanan): ?>
<div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
    <thead><tr><th>No. pesanan</th><th>Penyewa</th><th>Kamar</th><th>Mulai</th><th>Pembayaran</th><th>Nilai</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($pesanan as $r): ?>
        <tr>
            <td class="fw-semibold"><?= esc($r['no_pesan']) ?></td>
            <td><?= esc($r['nama_user']) ?><div class="small text-secondary"><?= esc($r['no_telp']) ?></div></td>
            <td><?= esc($r['nm_kos']) ?><div class="small text-secondary"><?= esc($r['nm_tipe']) ?> · <?= (int) $r['durasi'] ?> <?= ['harian' => 'hari', 'bulanan' => 'bulan', 'tahunan' => 'tahun'][$r['satuan_durasi']] ?? '' ?></div></td>
            <td><?= esc(date('d M Y', strtotime($r['tgl_mulai']))) ?></td>
            <td>
                <?php if ($r['metode_bayar']): ?>
                    <div class="small text-capitalize"><?= esc(str_replace('_', ' ', $r['metode_bayar'])) ?></div>
                    <span class="kq-badge <?= badge_status($r['status_bayar']) ?>"><?= esc($r['status_bayar']) ?></span>
                    <?php if ($r['bukti_transfer']): ?><div><a class="small fw-semibold" target="_blank" href="<?= base_url('uploads/bukti/' . rawurlencode($r['bukti_transfer'])) ?>"><i class="bi bi-image"></i> Lihat bukti</a></div><?php endif ?>
                    <?php if ($r['ref_gateway']): ?><div class="small text-secondary"><?= esc($r['ref_gateway']) ?></div><?php endif ?>
                <?php else: ?><span class="text-secondary small">Belum bayar</span><?php endif ?>
            </td>
            <td><?= rupiah($r['sub_total']) ?><div class="small text-secondary">total <?= rupiah($r['total_bayar']) ?></div></td>
            <td><span class="kq-badge <?= badge_status($r['status_pesan']) ?>"><?= esc($r['status_pesan']) ?></span></td>
            <td class="text-nowrap table-actions">
                <?php if ($r['status_pesan'] === 'Menunggu Verifikasi'): ?>
                    <form action="<?= site_url('/mitra/pesanan/verifikasi/' . rawurlencode($r['no_pesan']) . '/terima') ?>" method="post" onsubmit="return confirm('Verifikasi pembayaran & terima pesanan ini?')"><?= csrf_field() ?><button class="btn btn-success btn-sm" type="submit">Verifikasi &amp; Terima</button></form>
                    <form action="<?= site_url('/mitra/pesanan/verifikasi/' . rawurlencode($r['no_pesan']) . '/tolak') ?>" method="post" onsubmit="return confirm('Tolak pesanan ini? Pembayaran akan di-refund.')"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm" type="submit">Tolak</button></form>
                <?php else: ?><span class="text-secondary small">—</span><?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table></div></div>
<?php else: ?><div class="kq-empty"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada pesanan masuk.</div><?php endif ?>
<?= $this->endSection() ?>
