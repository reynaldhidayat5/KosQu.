<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Daftar<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $roleLama = old('role') ?? 'customer'; ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="kq-card p-4 p-lg-5">
            <h1 class="kq-h1">Buat akun KosQu</h1>
            <p class="kq-lead">Daftar sebagai pencari kos, atau jadi mitra untuk memasarkan kos Anda.</p>
            <form action="<?= site_url('/register') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Daftar sebagai</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <label class="kq-pay mb-0 flex-fill"><input type="radio" name="role" value="customer" class="form-check-input mt-0" <?= $roleLama !== 'mitra' ? 'checked' : '' ?>><span><b>Pencari kos</b><br><small class="text-secondary">Cari &amp; pesan kos</small></span></label>
                            <label class="kq-pay mb-0 flex-fill"><input type="radio" name="role" value="mitra" class="form-check-input mt-0" <?= $roleLama === 'mitra' ? 'checked' : '' ?>><span><b>Mitra / pemilik kos</b><br><small class="text-secondary">Perlu verifikasi KYC</small></span></label>
                        </div>
                    </div>
                    <div class="col-md-6"><label for="nama_user" class="form-label fw-semibold">Nama lengkap</label><input id="nama_user" name="nama_user" class="form-control" maxlength="40" value="<?= old('nama_user') ?>" required></div>
                    <div class="col-md-6"><label for="username" class="form-label fw-semibold">Username</label><input id="username" name="username" class="form-control" maxlength="25" value="<?= old('username') ?>" autocomplete="username" required></div>
                    <div class="col-md-6"><label for="email" class="form-label fw-semibold">Email</label><input id="email" name="email" type="email" class="form-control" maxlength="50" value="<?= old('email') ?>" autocomplete="email" required></div>
                    <div class="col-md-6"><label for="no_telp" class="form-label fw-semibold">Nomor telepon</label><input id="no_telp" name="no_telp" type="tel" class="form-control" maxlength="15" value="<?= old('no_telp') ?>" required></div>
                    <div class="col-md-6"><label for="password" class="form-label fw-semibold">Kata sandi</label><input id="password" name="password" type="password" class="form-control" minlength="6" autocomplete="new-password" required><div class="form-text">Minimal 6 karakter.</div></div>
                    <div class="col-md-6"><label for="alamat_user" class="form-label fw-semibold">Alamat</label><textarea id="alamat_user" name="alamat_user" class="form-control" rows="2"><?= old('alamat_user') ?></textarea></div>
                </div>

                <fieldset id="mitraFields" class="border rounded-3 p-3 mt-4 d-none">
                    <legend class="float-none w-auto px-2 fs-6 fw-bold">Data mitra &amp; rekening pencairan</legend>
                    <div class="row g-3">
                        <div class="col-md-6"><label for="no_ktp" class="form-label fw-semibold">Nomor KTP (16 digit)</label><input id="no_ktp" name="no_ktp" class="form-control" maxlength="16" inputmode="numeric" value="<?= old('no_ktp') ?>"></div>
                        <div class="col-md-6"><label for="foto_ktp" class="form-label fw-semibold">Foto KTP</label><input id="foto_ktp" name="foto_ktp" type="file" class="form-control" accept="image/*"></div>
                        <div class="col-md-4"><label for="nama_bank" class="form-label fw-semibold">Nama bank</label><input id="nama_bank" name="nama_bank" class="form-control" maxlength="30" value="<?= old('nama_bank') ?>"></div>
                        <div class="col-md-4"><label for="no_rekening" class="form-label fw-semibold">Nomor rekening</label><input id="no_rekening" name="no_rekening" class="form-control" maxlength="25" inputmode="numeric" value="<?= old('no_rekening') ?>"></div>
                        <div class="col-md-4"><label for="atas_nama" class="form-label fw-semibold">Atas nama</label><input id="atas_nama" name="atas_nama" class="form-control" maxlength="40" value="<?= old('atas_nama') ?>"></div>
                    </div>
                </fieldset>
                <button class="btn btn-primary btn-lg w-100 mt-4" type="submit">Buat akun</button>
            </form>
            <p class="text-center text-secondary mt-4 mb-0">Sudah punya akun? <a class="fw-semibold" href="<?= site_url('/login') ?>">Masuk</a></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    const radios = document.querySelectorAll('input[name="role"]');
    const box = document.getElementById('mitraFields');
    const inputs = box.querySelectorAll('input');
    function sync() {
        const mitra = document.querySelector('input[name="role"]:checked').value === 'mitra';
        box.classList.toggle('d-none', !mitra);
        inputs.forEach(i => { i.required = mitra; });
    }
    radios.forEach(r => r.addEventListener('change', sync));
    sync();
</script>
<?= $this->endSection() ?>
