<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Data User<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Data user</h1><p class="kq-lead">Kelola akun customer, mitra, dan admin.</p>
<form class="d-flex gap-2 mb-3" method="get" action="<?= site_url('/admin/user') ?>" style="max-width:420px"><input name="cari" value="<?= esc($cari) ?>" class="form-control" placeholder="Cari nama / email / username"><button class="btn btn-primary" type="submit">Cari</button></form>
<div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
    <thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Telepon</th><th>Role</th><th>Status</th><th></th></tr></thead>
    <tbody><?php foreach ($users as $u): ?>
        <tr><td class="fw-semibold"><?= esc($u['nama_user']) ?></td><td><?= esc($u['username']) ?></td><td><?= esc($u['email']) ?></td><td><?= esc($u['no_telp']) ?></td>
            <td class="text-capitalize"><?= esc($u['role']) ?></td><td><span class="kq-badge <?= badge_status($u['status_user']) ?>"><?= esc($u['status_user']) ?></span></td>
            <td class="table-actions"><?php if ((int) $u['id'] !== (int) session()->get('id')): ?><form action="<?= site_url('/admin/user/status/' . (int) $u['id']) ?>" method="post"><?= csrf_field() ?><button class="btn btn-sm <?= $u['status_user'] === 'aktif' ? 'btn-outline-danger' : 'btn-outline-success' ?>" type="submit"><?= $u['status_user'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?></button></form><?php endif ?></td></tr>
    <?php endforeach ?><?php if (! $users): ?><tr><td colspan="7" class="text-center text-secondary py-4">Tidak ada data.</td></tr><?php endif ?></tbody>
</table></div></div>
<?= $this->endSection() ?>
