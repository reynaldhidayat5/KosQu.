<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Akun Saya<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $v = static fn (string $k, $d = '') => old($k) ?? esc((string) $d); ?>
<div class="row justify-content-center"><div class="col-lg-8">
    <h1 class="kq-h1">Akun saya</h1>
    <p class="kq-lead">Ubah data profil<?= $mitra ? ' dan rekening pencairan dana' : '' ?>.</p>
    <form action="<?= site_url('/akun/simpan') ?>" method="post" class="kq-card p-4">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input class="form-control" value="<?= esc($user['email']) ?>" disabled></div>
            <div class="col-md-6"><label class="form-label fw-semibold">Username</label><input class="form-control" value="<?= esc($user['username']) ?>" disabled></div>
            <div class="col-md-6"><label for="nama_user" class="form-label fw-semibold">Nama lengkap</label><input id="nama_user" name="nama_user" class="form-control" maxlength="40" value="<?= $v('nama_user', $user['nama_user']) ?>" required></div>
            <div class="col-md-6"><label for="no_telp" class="form-label fw-semibold">Nomor telepon</label><input id="no_telp" name="no_telp" class="form-control" maxlength="15" value="<?= $v('no_telp', $user['no_telp']) ?>" required></div>
            <div class="col-12"><label for="alamat_user" class="form-label fw-semibold">Alamat</label><textarea id="alamat_user" name="alamat_user" class="form-control" rows="2"><?= $v('alamat_user', $user['alamat_user']) ?></textarea></div>
            <div class="col-md-6"><label for="password" class="form-label fw-semibold">Kata sandi baru</label><input id="password" name="password" type="password" class="form-control" minlength="6" autocomplete="new-password"><div class="form-text">Kosongkan bila tidak diganti.</div></div>
        </div>
        <?php if ($mitra): ?>
            <hr class="my-4">
            <div class="d-flex justify-content-between"><h2 class="h5">Data rekening mitra</h2><span class="kq-badge <?= badge_status($mitra['status_kyc']) ?>">KYC: <?= esc($mitra['status_kyc']) ?></span></div>
            <div class="row g-3 mt-1">
                <div class="col-md-4"><label for="nama_bank" class="form-label fw-semibold">Nama bank</label><input id="nama_bank" name="nama_bank" class="form-control" maxlength="30" value="<?= $v('nama_bank', $mitra['nama_bank']) ?>" required></div>
                <div class="col-md-4"><label for="no_rekening" class="form-label fw-semibold">Nomor rekening</label><input id="no_rekening" name="no_rekening" class="form-control" maxlength="25" value="<?= $v('no_rekening', $mitra['no_rekening']) ?>" required></div>
                <div class="col-md-4"><label for="atas_nama" class="form-label fw-semibold">Atas nama</label><input id="atas_nama" name="atas_nama" class="form-control" maxlength="40" value="<?= $v('atas_nama', $mitra['atas_nama']) ?>" required></div>
            </div>
        <?php endif ?>
        <button class="btn btn-primary mt-4 px-4" type="submit">Simpan perubahan</button>
    </form>
</div></div>
<?= $this->endSection() ?>
