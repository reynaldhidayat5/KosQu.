<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Instruksi Pembayaran<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $manual = $bayar['metode_bayar'] === 'transfer_manual'; ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="kq-card p-4">
            <?php if ($manual): ?>
                <h1 class="kq-h1">Transfer manual</h1>
                <p class="kq-lead">Transfer sesuai total ke rekening tujuan, lalu unggah bukti transfer.</p>
                <div class="kq-summary mb-4">
                    <div class="small text-secondary">Rekening tujuan</div>
                    <div class="h5 fw-bold mb-2"><?= esc($rekening) ?></div>
                    <div class="small text-secondary">Jumlah transfer</div>
                    <div class="h4 fw-bold mb-0 text-primary"><?= rupiah($p['total_bayar']) ?></div>
                </div>
                <form action="<?= site_url('/customer/bayar/' . rawurlencode($p['no_pesan']) . '/bukti') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <label for="bukti_transfer" class="form-label fw-semibold">Unggah bukti transfer</label>
                    <input id="bukti_transfer" name="bukti_transfer" type="file" class="form-control mb-3" accept="image/*" required>
                    <button class="btn btn-primary btn-lg w-100" type="submit">Kirim bukti pembayaran</button>
                </form>
            <?php else: ?>
                <h1 class="kq-h1"><?= $bayar['metode_bayar'] === 'va' ? 'Virtual Account' : 'E-Wallet' ?></h1>
                <p class="kq-lead">Transaksi dibuat di Payment Gateway. Selesaikan pembayaran sebelum batas waktu.</p>
                <div class="kq-summary mb-4">
                    <div class="small text-secondary"><?= $bayar['metode_bayar'] === 'va' ? 'Nomor Virtual Account' : 'Kode pembayaran e-wallet' ?></div>
                    <div class="h3 fw-bold mb-2" style="letter-spacing:.04em"><?= esc($bayar['ref_gateway']) ?></div>
                    <div class="small text-secondary">Jumlah pembayaran</div>
                    <div class="h4 fw-bold mb-0 text-primary"><?= rupiah($p['total_bayar']) ?></div>
                </div>
                <div class="alert alert-info small">
                    <b>Mode sandbox.</b> Integrasi Midtrans/Xendit belum dipasang. Tombol di bawah mensimulasikan notifikasi
                    <i>webhook settlement</i> dari gateway, sehingga status pesanan berubah seperti pada Sequence Diagram.
                </div>
                <form action="<?= site_url('/customer/bayar/' . rawurlencode($p['no_pesan']) . '/simulasi') ?>" method="post">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary btn-lg w-100" type="submit"><i class="bi bi-check2-circle"></i> Simulasikan pembayaran berhasil</button>
                </form>
            <?php endif ?>
            <a class="btn btn-link w-100 mt-2" href="<?= site_url('/customer/bayar/' . rawurlencode($p['no_pesan'])) ?>">Ganti metode pembayaran</a>
        </div>
    </div>
    <div class="col-lg-5"><?= view('customer/_ringkasan', ['p' => $p]) ?></div>
</div>
<?= $this->endSection() ?>
