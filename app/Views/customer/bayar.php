<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Pembayaran<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="kq-card p-4">
            <h1 class="kq-h1">Pilih metode pembayaran</h1>
            <p class="kq-lead">Selesaikan sebelum <b><?= esc(date('d M Y H:i', strtotime($p['batas_bayar']))) ?></b> agar pesanan tidak dibatalkan otomatis.</p>
            <form action="<?= site_url('/customer/bayar/' . rawurlencode($p['no_pesan']) . '/metode') ?>" method="post">
                <?= csrf_field() ?>
                <label class="kq-pay"><input class="form-check-input mt-0" type="radio" name="metode_bayar" value="transfer_manual" required>
                    <span><b><i class="bi bi-bank"></i> Transfer bank manual</b><br><small class="text-secondary">Transfer lalu unggah bukti. Diverifikasi oleh mitra.</small></span></label>
                <label class="kq-pay"><input class="form-check-input mt-0" type="radio" name="metode_bayar" value="va">
                    <span><b><i class="bi bi-credit-card-2-front"></i> Virtual Account</b><br><small class="text-secondary">Dibuat otomatis lewat Payment Gateway.</small></span></label>
                <label class="kq-pay"><input class="form-check-input mt-0" type="radio" name="metode_bayar" value="ewallet">
                    <span><b><i class="bi bi-wallet2"></i> E-Wallet</b><br><small class="text-secondary">Bayar lewat dompet digital.</small></span></label>
                <button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Lanjutkan</button>
            </form>
        </div>
    </div>
    <div class="col-lg-5"><?= view('customer/_ringkasan', ['p' => $p]) ?></div>
</div>
<?= $this->endSection() ?>
