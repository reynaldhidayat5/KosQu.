<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Data Mitra<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Data mitra</h1><p class="kq-lead">Daftar pemilik kos yang terdaftar.</p>
<div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
    <thead><tr><th>Kode</th><th>Nama</th><th>Kontak</th><th>Rekening</th><th>Kos</th><th>Saldo</th><th>KYC</th></tr></thead>
    <tbody><?php foreach ($daftar as $m): ?>
        <tr><td><?= esc($m['kd_mitra']) ?></td><td class="fw-semibold"><?= esc($m['nama_user']) ?></td><td><?= esc($m['email']) ?><div class="small text-secondary"><?= esc($m['no_telp']) ?></div></td>
            <td><?= esc($m['nama_bank']) ?> <?= esc($m['no_rekening']) ?><div class="small text-secondary">a.n. <?= esc($m['atas_nama']) ?></div></td><td><?= (int) $m['jml_kos'] ?></td><td><?= rupiah($m['saldo']) ?></td>
            <td><span class="kq-badge <?= badge_status($m['status_kyc']) ?>"><?= esc($m['status_kyc']) ?></span></td></tr>
    <?php endforeach ?><?php if (! $daftar): ?><tr><td colspan="7" class="text-center text-secondary py-4">Belum ada mitra.</td></tr><?php endif ?></tbody>
</table></div></div>
<?= $this->endSection() ?>
