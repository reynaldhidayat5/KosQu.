<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Komisi Platform<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="row justify-content-center"><div class="col-lg-6">
<h1 class="kq-h1">Pengaturan platform</h1><p class="kq-lead">Komisi dipotong dari setiap pencairan dana mitra.</p>
<form action="<?= site_url('/admin/pengaturan/simpan') ?>" method="post" class="kq-card p-4">
    <?= csrf_field() ?>
    <div class="mb-3"><label for="komisi_persen" class="form-label fw-semibold">Komisi platform (%)</label><input id="komisi_persen" name="komisi_persen" type="number" step="0.1" min="0" max="50" class="form-control" value="<?= nilai('komisi_persen', $p['komisi_persen']) ?>" required></div>
    <div class="mb-3"><label for="biaya_layanan" class="form-label fw-semibold">Biaya layanan per pesanan (Rp)</label><input id="biaya_layanan" name="biaya_layanan" type="number" min="0" class="form-control" value="<?= nilai('biaya_layanan', $p['biaya_layanan']) ?>" required></div>
    <div class="mb-3"><label for="rekening_platform" class="form-label fw-semibold">Rekening tujuan transfer manual</label><input id="rekening_platform" name="rekening_platform" class="form-control" maxlength="255" value="<?= nilai('rekening_platform', $p['rekening_platform']) ?>" required><div class="form-text">Ditampilkan ke customer saat memilih transfer manual.</div></div>
    <button class="btn btn-primary px-4" type="submit">Simpan</button>
</form></div></div>
<?= $this->endSection() ?>
