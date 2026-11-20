<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?><?= esc($kos['nm_kos']) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$labelTipe = ['putra' => 'Putra', 'putri' => 'Putri', 'campur' => 'Campur'];
$role      = session()->get('role');
$lat = $kos['latitude'] ?? ''; $lng = $kos['longitude'] ?? '';
$adaPeta = is_numeric($lat) && is_numeric($lng);
$rata = $ulasan ? round(array_sum(array_column($ulasan, 'rating')) / count($ulasan), 1) : null;
?>
<nav class="small mb-3"><a href="<?= site_url('/') ?>"><i class="bi bi-arrow-left"></i> Kembali ke pencarian</a></nav>

<!-- Foto -->
<?php if (count($foto) >= 2): ?>
    <div class="kq-gallery mb-4">
        <div class="big"><img src="<?= esc(foto_kos_url($foto[0]['url_foto'])) ?>" alt="Foto <?= esc($kos['nm_kos']) ?>"></div>
        <div><img src="<?= esc(foto_kos_url($foto[1]['url_foto'])) ?>" alt=""></div>
        <div><img src="<?= esc(foto_kos_url($foto[2]['url_foto'] ?? $foto[1]['url_foto'])) ?>" alt=""></div>
    </div>
<?php else: ?>
    <div class="kq-gallery solo mb-4"><div class="big"><img src="<?= esc(foto_kos_url($foto[0]['url_foto'] ?? null)) ?>" alt="Foto <?= esc($kos['nm_kos']) ?>"></div></div>
<?php endif ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="kq-card mb-4">
            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                <span class="kq-tag <?= esc($kos['tipe_kos']) ?>" style="position:static;border-radius:.4rem"><?= esc($labelTipe[$kos['tipe_kos']] ?? '') ?></span>
                <?php if ($rata): ?><span class="kq-rate"><i class="bi bi-star-fill"></i> <?= $rata ?> <span class="text-secondary fw-normal">(<?= count($ulasan) ?> ulasan)</span></span><?php endif ?>
            </div>
            <h1 class="kq-h1"><?= esc($kos['nm_kos']) ?></h1>
            <p class="text-secondary mb-1"><i class="bi bi-geo-alt"></i> <?= esc($kos['alamat_kos']) ?>, <?= esc($kos['kota']) ?></p>
            <p class="small text-secondary mb-0"><i class="bi bi-person-check"></i> Dikelola <?= esc($kos['nama_pemilik']) ?> · Mitra terverifikasi</p>

            <?php if (! empty($kos['deskripsi'])): ?><hr><h2 class="h5">Tentang kos</h2><p class="mb-0"><?= nl2br(esc($kos['deskripsi'])) ?></p><?php endif ?>
            <?php if ($fasilitas): ?>
                <hr><h2 class="h5 mb-3">Fasilitas</h2>
                <div class="kq-fas"><?php foreach ($fasilitas as $f): ?><span><?= esc(trim(($f['ikon_fas'] ?? '') . ' ' . $f['nm_fas'])) ?></span><?php endforeach ?></div>
            <?php endif ?>
            <?php if (! empty($kos['aturan_kos'])): ?><hr><h2 class="h5">Aturan kos</h2><p class="mb-0"><?= nl2br(esc($kos['aturan_kos'])) ?></p><?php endif ?>
        </div>

        <div class="kq-card mb-4">
            <h2 class="h5 mb-3">Lokasi</h2>
            <?php if ($adaPeta): ?>
                <div class="ratio ratio-16x9 rounded-3 overflow-hidden">
                    <iframe title="Peta lokasi kos" src="<?= esc('https://www.google.com/maps?q=' . rawurlencode($lat . ',' . $lng) . '&output=embed') ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
                <a class="d-inline-block mt-3 fw-semibold" href="<?= esc('https://www.google.com/maps/search/?api=1&query=' . rawurlencode($lat . ',' . $lng)) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right"></i> Buka di Google Maps</a>
            <?php else: ?>
                <p class="text-secondary mb-0">Titik peta belum diatur oleh pemilik kos.</p>
            <?php endif ?>
        </div>

        <div class="kq-card">
            <h2 class="h5 mb-3">Ulasan penghuni</h2>
            <?php if ($ulasan): foreach ($ulasan as $u): ?>
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between"><b><?= esc($u['nama_user']) ?></b><span class="kq-rate"><?= str_repeat('★', (int) $u['rating']) ?><span class="text-secondary"><?= str_repeat('★', 5 - (int) $u['rating']) ?></span></span></div>
                    <div class="small text-secondary mb-1"><?= esc($u['tgl_review']) ?></div>
                    <div><?= nl2br(esc($u['komentar'])) ?></div>
                </div>
            <?php endforeach; else: ?><p class="text-secondary mb-0">Belum ada ulasan.</p><?php endif ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="kq-card kq-sticky">
            <h2 class="h5 mb-3">Pilih tipe kamar</h2>
            <?php foreach ($tipe_kamar as $t): ?>
                <?php $bisa = (int) $t['kamar_tersedia'] > 0 && $t['status_tipe'] !== 'penuh'; ?>
                <div class="kq-room">
                    <div class="d-flex justify-content-between align-items-start">
                        <b><?= esc($t['nm_tipe']) ?></b>
                        <?php if ($bisa): ?><span class="kq-badge ok"><?= (int) $t['kamar_tersedia'] ?> kamar tersedia</span><?php else: ?><span class="kq-badge bad">Penuh</span><?php endif ?>
                    </div>
                    <div class="p mt-2"><?= $t['harga_bulanan'] ? rupiah($t['harga_bulanan']) . ' <small class="fw-normal text-secondary">/bulan</small>' : '<small class="text-secondary">Harga bulanan belum diatur</small>' ?></div>
                    <div class="small text-secondary mb-3">
                        <?= $t['harga_harian'] ? 'Harian ' . rupiah($t['harga_harian']) : '' ?>
                        <?= ($t['harga_harian'] && $t['harga_tahunan']) ? ' · ' : '' ?>
                        <?= $t['harga_tahunan'] ? 'Tahunan ' . rupiah($t['harga_tahunan']) : '' ?>
                    </div>
                    <?php $tujuan = '/customer/pesan/' . rawurlencode($t['kd_tipe']) . ($tgl ? '?tgl=' . rawurlencode($tgl) : ''); ?>
                    <?php if (! $bisa): ?>
                        <button class="btn btn-secondary w-100" disabled>Kamar penuh</button>
                    <?php elseif ($role === 'customer'): ?>
                        <a class="btn btn-primary w-100" href="<?= site_url($tujuan) ?>">Pesan Sekarang</a>
                    <?php elseif (! $role): ?>
                        <a class="btn btn-primary w-100" href="<?= site_url('/login') . '?next=' . rawurlencode($tujuan) ?>">Pesan Sekarang</a>
                    <?php else: ?>
                        <div class="small text-secondary">Pemesanan hanya tersedia untuk akun pencari kos.</div>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
            <?php if (! $tipe_kamar): ?><p class="text-secondary mb-0">Belum ada tipe kamar untuk kos ini.</p><?php endif ?>
            <?php if (! $role && $tipe_kamar): ?><div class="small text-secondary text-center">Belum punya akun? <a href="<?= site_url('/register') ?>" class="fw-semibold">Daftar gratis</a></div><?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
