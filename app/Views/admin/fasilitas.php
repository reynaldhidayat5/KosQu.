<?php
/** @var array<int, array{kd_fas: string, ikon_fas: ?string, nm_fas: string}> $daftar */
/** @var ?array{kd_fas: string, ikon_fas: ?string, nm_fas: string} $edit */
?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Data Fasilitas<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Data fasilitas</h1><p class="kq-lead">Master fasilitas yang bisa dipilih mitra dan dipakai sebagai filter pencarian.</p>
<div class="row g-4">
    <div class="col-lg-4"><form action="<?= site_url('/admin/fasilitas/simpan') ?>" method="post" class="kq-card">
        <?= csrf_field() ?><input type="hidden" name="kd_fas" value="<?= esc($edit['kd_fas'] ?? '') ?>">
        <h2 class="h5 mb-3"><?= $edit ? 'Ubah fasilitas' : 'Tambah fasilitas' ?></h2>
        <div class="mb-3"><label class="form-label fw-semibold" for="nm_fas">Nama fasilitas</label><input id="nm_fas" name="nm_fas" class="form-control" maxlength="30" value="<?= nilai('nm_fas', $edit['nm_fas'] ?? '') ?>" required></div>
        <div class="mb-3"><label class="form-label fw-semibold" for="ikon_fas">Ikon (emoji)</label><input id="ikon_fas" name="ikon_fas" class="form-control" maxlength="50" value="<?= nilai('ikon_fas', $edit['ikon_fas'] ?? '') ?>" placeholder="❄️"></div>
        <button class="btn btn-primary" type="submit">Simpan</button> <?php if ($edit): ?><a class="btn btn-outline-secondary" href="<?= site_url('/admin/fasilitas') ?>">Batal</a><?php endif ?>
    </form></div>
    <div class="col-lg-8"><div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
        <thead><tr><th>Kode</th><th>Ikon</th><th>Nama</th><th></th></tr></thead>
        <tbody><?php foreach ($daftar as $f): ?>
            <tr><td><?= esc($f['kd_fas']) ?></td><td><?= esc($f['ikon_fas']) ?></td><td class="fw-semibold"><?= esc($f['nm_fas']) ?></td>
                <td class="text-nowrap table-actions"><a class="btn btn-sm btn-outline-primary" href="<?= site_url('/admin/fasilitas/edit/' . rawurlencode($f['kd_fas'])) ?>">Ubah</a>
                <form action="<?= site_url('/admin/fasilitas/hapus/' . rawurlencode($f['kd_fas'])) ?>" method="post" onsubmit="return confirm('Hapus fasilitas ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button></form></td></tr>
        <?php endforeach ?><?php if (! $daftar): ?><tr><td colspan="4" class="text-center text-secondary py-4">Belum ada fasilitas.</td></tr><?php endif ?></tbody>
    </table></div></div></div>
</div>
<?= $this->endSection() ?>
