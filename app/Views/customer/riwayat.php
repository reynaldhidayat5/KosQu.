<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Riwayat Pesanan<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Riwayat pesanan</h1>
<p class="kq-lead">Pantau status pemesanan dan pembayaran kos Anda.</p>
<?php $satuan = ['harian' => 'hari', 'bulanan' => 'bulan', 'tahunan' => 'tahun']; ?>
<?php if ($pesanan): ?>
    <div class="vstack gap-3">
    <?php foreach ($pesanan as $r): ?>
        <div class="kq-card">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                <div>
                    <div class="small text-secondary"><?= esc($r['no_pesan']) ?></div>
                    <h2 class="h5 mb-0"><a href="<?= site_url('kos/detail/' . rawurlencode($r['kd_kos'])) ?>" class="text-reset"><?= esc($r['nm_kos']) ?></a></h2>
                    <div class="text-secondary small"><?= esc($r['nm_tipe']) ?> · mulai <?= esc(date('d M Y', strtotime($r['tgl_mulai']))) ?> · <?= (int) $r['durasi'] ?> <?= $satuan[$r['satuan_durasi']] ?? '' ?></div>
                </div>
                <div class="text-end">
                    <span class="kq-badge <?= badge_status($r['status_pesan']) ?>"><?= esc($r['status_pesan']) ?></span>
                    <div class="fw-bold mt-1"><?= rupiah($r['total_bayar']) ?></div>
                </div>
            </div>
            <?php if ($r['status_pesan'] === 'Menunggu Pembayaran'): ?>
                <div class="small text-secondary mb-2"><i class="bi bi-clock"></i> Bayar sebelum <?= esc(date('d M Y H:i', strtotime($r['batas_bayar']))) ?></div>
            <?php elseif ($r['status_pesan'] === 'Menunggu Verifikasi'): ?>
                <div class="small text-secondary mb-2"><i class="bi bi-hourglass-split"></i> Pembayaran diterima, menunggu konfirmasi pemilik kos.</div>
            <?php elseif ($r['status_pesan'] === 'Ditolak'): ?>
                <div class="small text-danger mb-2"><i class="bi bi-x-circle"></i> Pesanan ditolak pemilik kos. Pembayaran Anda akan di-refund.</div>
            <?php endif ?>
            <div class="d-flex flex-wrap gap-2 table-actions">
                <?php if ($r['status_pesan'] === 'Menunggu Pembayaran'): ?>
                    <a class="btn btn-primary btn-sm" href="<?= site_url('/customer/bayar/' . rawurlencode($r['no_pesan'])) ?>">Bayar</a>
                    <form action="<?= site_url('/customer/batal/' . rawurlencode($r['no_pesan'])) ?>" method="post" onsubmit="return confirm('Batalkan pesanan ini?')">
                        <?= csrf_field() ?><button class="btn btn-outline-danger btn-sm" type="submit">Batalkan</button></form>
                <?php endif ?>
                <?php if ($r['status_pesan'] === 'Berhasil'): ?>
                    <a class="btn btn-outline-primary btn-sm" target="_blank" href="<?= site_url('/customer/invoice/' . rawurlencode($r['no_pesan'])) ?>"><i class="bi bi-download"></i> E-kuitansi</a>
                    <?php if (! (int) $r['sudah_review']): ?>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#rv<?= esc($r['no_pesan']) ?>"><i class="bi bi-star"></i> Beri ulasan</button>
                    <?php else: ?><span class="kq-badge ok align-self-center">Sudah diulas</span><?php endif ?>
                <?php endif ?>
            </div>
            <?php if ($r['status_pesan'] === 'Berhasil' && ! (int) $r['sudah_review']): ?>
                <div class="collapse mt-3" id="rv<?= esc($r['no_pesan']) ?>">
                    <form action="<?= site_url('/customer/review/' . rawurlencode($r['no_pesan'])) ?>" method="post" class="row g-2">
                        <?= csrf_field() ?>
                        <div class="col-sm-3"><select name="rating" class="form-select" required>
                            <option value="">Rating</option><?php foreach ([5, 4, 3, 2, 1] as $n): ?><option value="<?= $n ?>"><?= str_repeat('★', $n) ?></option><?php endforeach ?></select></div>
                        <div class="col-sm-7"><input name="komentar" class="form-control" maxlength="500" placeholder="Ceritakan pengalaman Anda" required></div>
                        <div class="col-sm-2"><button class="btn btn-primary w-100" type="submit">Kirim</button></div>
                    </form>
                </div>
            <?php endif ?>
        </div>
    <?php endforeach ?>
    </div>
<?php else: ?>
    <div class="kq-empty"><i class="bi bi-receipt fs-2 d-block mb-2"></i><b>Belum ada pesanan</b><br>Pesanan yang Anda buat akan muncul di sini.<br><a class="btn btn-primary mt-3" href="<?= site_url('/') ?>">Cari kos</a></div>
<?php endif ?>
<?= $this->endSection() ?>
