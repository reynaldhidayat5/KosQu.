<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Data Kos<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Data kos</h1><p class="kq-lead">Pantau seluruh kos dan atur status tayangnya.</p>
<div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
    <thead><tr><th>Kode</th><th>Nama kos</th><th>Pemilik</th><th>Kota</th><th>Tipe</th><th>Status</th><th></th></tr></thead>
    <tbody><?php foreach ($daftar as $k): ?>
        <tr><td><?= esc($k['kd_kos']) ?></td><td class="fw-semibold"><a class="text-reset" href="<?= site_url('kos/detail/' . rawurlencode($k['kd_kos'])) ?>"><?= esc($k['nm_kos']) ?></a></td><td><?= esc($k['nama_pemilik']) ?> <span class="kq-badge <?= badge_status($k['status_kyc']) ?>"><?= esc($k['status_kyc']) ?></span></td>
            <td><?= esc($k['kota']) ?></td><td class="text-capitalize"><?= esc($k['tipe_kos']) ?></td><td><span class="kq-badge <?= badge_status($k['status_kos']) ?>"><?= esc($k['status_kos']) ?></span></td>
            <td class="table-actions"><form action="<?= site_url('/admin/kos/status/' . rawurlencode($k['kd_kos'])) ?>" method="post"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary" type="submit"><?= $k['status_kos'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?></button></form></td></tr>
    <?php endforeach ?><?php if (! $daftar): ?><tr><td colspan="7" class="text-center text-secondary py-4">Belum ada kos.</td></tr><?php endif ?></tbody>
</table></div></div>
<?= $this->endSection() ?>
