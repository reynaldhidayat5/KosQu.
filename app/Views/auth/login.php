<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Masuk<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="kq-card p-4 p-lg-5">
            <h1 class="kq-h1">Masuk ke KosQu</h1>
            <p class="kq-lead">Pencari kos, mitra pemilik kos, dan admin masuk dari sini.</p>
            <form action="<?= site_url('/login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input id="email" name="email" type="email" class="form-control form-control-lg" value="<?= old('email') ?>" autocomplete="email" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Kata sandi</label>
                    <input id="password" name="password" type="password" class="form-control form-control-lg" autocomplete="current-password" required>
                </div>
                <button class="btn btn-primary btn-lg w-100" type="submit">Masuk</button>
            </form>
            <p class="text-center text-secondary mt-4 mb-0">Belum punya akun? <a class="fw-semibold" href="<?= site_url('/register') ?>">Daftar sekarang</a></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
