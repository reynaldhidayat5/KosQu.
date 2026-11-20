<?php
/** @var array{total_user: int, total_kos: int, pesanan_aktif: int, kyc_pending: int, payout_baru: int, transaksi: int|float} $stats */
?>

<?= $this->extend('layouts/main') ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Dashboard Admin<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Dashboard admin</h1><p class="kq-lead">Ringkasan aktivitas platform KosQu.</p>
<div class="row g-3 mb-4">
    <?php foreach (['total_user' => 'Total pengguna', 'total_kos' => 'Total kos', 'pesanan_aktif' => 'Pesanan aktif', 'kyc_pending' => 'KYC menunggu', 'payout_baru' => 'Payout diajukan'] as $k => $l): ?>
        <div class="col-6 col-lg"><div class="kq-stat"><div class="l"><?= $l ?></div><div class="v"><?= (int) $stats[$k] ?></div></div></div>
    <?php endforeach ?>
</div>
<div class="kq-stat mb-4"><div class="l">Nilai transaksi berhasil</div><div class="v"><?= rupiah($stats['transaksi']) ?></div></div>
<div class="kq-card"><h2 class="h5 mb-3">Administrasi</h2><div class="d-flex flex-wrap gap-2">
    <a class="btn btn-primary" href="<?= site_url('/admin/kyc') ?>">Verifikasi KYC</a>
    <a class="btn btn-outline-primary" href="<?= site_url('/admin/payout') ?>">Proses payout</a>
    <a class="btn btn-outline-primary" href="<?= site_url('/admin/transaksi') ?>">Pantau transaksi</a>
    <a class="btn btn-outline-primary" href="<?= site_url('/admin/pengaturan') ?>">Komisi platform</a>
    <a class="btn btn-outline-primary" href="<?= site_url('/admin/laporan') ?>">Laporan</a>
</div></div>
<?= $this->endSection() ?>
