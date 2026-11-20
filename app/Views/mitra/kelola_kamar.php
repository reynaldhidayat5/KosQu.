<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Tipe Kamar<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $edit = (bool) $tipe; $kosPilih = old('kd_kos') ?? ($tipe['kd_kos'] ?? ''); ?>
<h1 class="kq-h1">Tipe kamar, harga &amp; ketersediaan</h1>
<p class="kq-lead">Atur kapasitas, jumlah kamar tersedia, dan harga sewa harian/bulanan/tahunan.</p>
<div class="row g-4">
    <div class="col-lg-5">
        <form action="<?= site_url('/mitra/kamar/simpan') ?>" method="post" class="kq-card">
            <?= csrf_field() ?>
            <input type="hidden" name="kd_tipe" value="<?= esc($tipe['kd_tipe'] ?? '') ?>">
            <h2 class="h5 mb-3"><?= $edit ? 'Ubah tipe kamar' : 'Tambah tipe kamar' ?></h2>
            <?php if (! $daftarKos): ?><div class="alert alert-warning">Tambahkan data kos terlebih dahulu di menu Data Kos.</div><?php endif ?>
            <div class="mb-3"><label for="kd_kos" class="form-label fw-semibold">Properti</label>
                <select id="kd_kos" name="kd_kos" class="form-select" required><?php foreach ($daftarKos as $k): ?><option value="<?= esc($k['kd_kos']) ?>" <?= $kosPilih === $k['kd_kos'] ? 'selected' : '' ?>><?= esc($k['nm_kos']) ?></option><?php endforeach ?></select></div>
            <div class="mb-3"><label for="nm_tipe" class="form-label fw-semibold">Nama tipe</label><input id="nm_tipe" name="nm_tipe" class="form-control" maxlength="30" value="<?= nilai('nm_tipe', $tipe['nm_tipe'] ?? '') ?>" required></div>
            <div class="row g-3">
                <div class="col-6"><label for="jml_kamar" class="form-label fw-semibold">Jumlah kamar</label><input id="jml_kamar" name="jml_kamar" type="number" min="0" class="form-control" value="<?= nilai('jml_kamar', $tipe['jml_kamar'] ?? 1) ?>" required></div>
                <div class="col-6"><label for="kamar_tersedia" class="form-label fw-semibold">Kamar tersedia</label><input id="kamar_tersedia" name="kamar_tersedia" type="number" min="0" class="form-control" value="<?= nilai('kamar_tersedia', $tipe['kamar_tersedia'] ?? 1) ?>" required></div>
                <?php foreach (['harga_harian' => 'Harga harian', 'harga_bulanan' => 'Harga bulanan', 'harga_tahunan' => 'Harga tahunan'] as $f => $l): ?>
                    <div class="col-12"><label for="<?= $f ?>" class="form-label fw-semibold"><?= $l ?> (Rp)</label><input id="<?= $f ?>" name="<?= $f ?>" type="number" min="0" class="form-control" value="<?= nilai($f, $tipe[$f] ?? 0) ?>" required></div>
                <?php endforeach ?>
            </div>
            <div class="d-flex gap-2 mt-4"><button class="btn btn-primary px-4" type="submit" <?= $daftarKos ? '' : 'disabled' ?>>Simpan</button>
                <?php if ($edit): ?><a class="btn btn-outline-secondary" href="<?= site_url('/mitra/kamar') ?>">Batal</a><?php endif ?></div>
        </form>
    </div>
    <div class="col-lg-7">
        <div class="kq-card">
            <h2 class="h5 mb-3">Tipe kamar terdaftar</h2>
            <?php if ($daftarTipe): ?>
                <div class="table-responsive"><table class="kq-table">
                    <thead><tr><th>Tipe</th><th>Kos</th><th>Harga/bulan</th><th>Tersedia</th><th>Status</th><th></th></tr></thead>
                    <tbody><?php foreach ($daftarTipe as $t): ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($t['nm_tipe']) ?></td><td><?= esc($t['nm_kos']) ?></td><td><?= rupiah($t['harga_bulanan']) ?></td>
                            <td><?= (int) $t['kamar_tersedia'] ?> / <?= (int) $t['jml_kamar'] ?></td>
                            <td><span class="kq-badge <?= badge_status($t['status_tipe']) ?>"><?= esc($t['status_tipe']) ?></span></td>
                            <td class="text-nowrap table-actions">
                                <a class="btn btn-outline-primary btn-sm" href="<?= site_url('/mitra/kamar/edit/' . rawurlencode($t['kd_tipe'])) ?>">Ubah</a>
                                <form action="<?= site_url('/mitra/kamar/hapus/' . rawurlencode($t['kd_tipe'])) ?>" method="post" onsubmit="return confirm('Hapus tipe kamar ini?')"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm" type="submit">Hapus</button></form>
                            </td>
                        </tr>
                    <?php endforeach ?></tbody>
                </table></div>
            <?php else: ?><p class="text-secondary mb-0">Belum ada tipe kamar.</p><?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
