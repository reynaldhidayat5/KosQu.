<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Dashboard Mitra<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div><h1 class="kq-h1">Dashboard mitra</h1><p class="kq-lead mb-0">Ringkasan properti dan aktivitas Anda.</p></div>
    <span class="kq-badge <?= badge_status($mitra['status_kyc']) ?> fs-6">KYC: <?= esc($mitra['status_kyc']) ?></span>
</div>
<?php if ($mitra['status_kyc'] !== 'verified'): ?>
    <div class="alert alert-warning kq-flash"><i class="bi bi-shield-exclamation"></i>
        <?= $mitra['status_kyc'] === 'rejected' ? 'Verifikasi KYC Anda ditolak. Perbarui data lalu hubungi admin.' : 'Akun Anda sedang menunggu verifikasi KYC oleh admin. Kos baru dapat ditayangkan setelah terverifikasi.' ?></div>
<?php endif ?>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="kq-stat"><div class="l">Total kos</div><div class="v"><?= (int) $stats['total_kos'] ?></div></div></div>
    <div class="col-md-4"><div class="kq-stat"><div class="l">Menunggu verifikasi</div><div class="v"><?= (int) $stats['pesanan_masuk'] ?></div></div></div>
    <div class="col-md-4"><div class="kq-stat"><div class="l">Saldo tersedia</div><div class="v"><?= rupiah($stats['saldo']) ?></div></div></div>
</div>
<div class="kq-card mb-4">
    <h2 class="h5 mb-3">Akses cepat</h2>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-primary" href="<?= site_url('/mitra/kos') ?>">Kelola kos</a>
        <a class="btn btn-outline-primary" href="<?= site_url('/mitra/kamar') ?>">Tipe kamar &amp; harga</a>
        <a class="btn btn-outline-primary" href="<?= site_url('/mitra/pesanan') ?>">Pesanan masuk</a>
        <a class="btn btn-outline-primary" href="<?= site_url('/mitra/penarikan') ?>">Tarik dana</a>
    </div>
</div>
<div class="kq-card">
    <h2 class="h5 mb-3">Pesanan terbaru</h2>
    <?php if ($terbaru): ?>
    <div class="table-responsive"><table class="kq-table">
        <thead><tr><th>No. pesanan</th><th>Penyewa</th><th>Kos</th><th>Total</th><th>Status</th></tr></thead>
        <tbody><?php foreach ($terbaru as $r): ?><tr><td><?= esc($r['no_pesan']) ?></td><td><?= esc($r['nama_user']) ?></td><td><?= esc($r['nm_kos']) ?></td><td><?= rupiah($r['total_bayar']) ?></td><td><span class="kq-badge <?= badge_status($r['status_pesan']) ?>"><?= esc($r['status_pesan']) ?></span></td></tr><?php endforeach ?></tbody>
    </table></div>
    <?php else: ?><p class="text-secondary mb-0">Belum ada pesanan.</p><?php endif ?>
</div>
<?= $this->endSection() ?>
