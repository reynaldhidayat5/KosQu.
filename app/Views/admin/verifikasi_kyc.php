<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Verifikasi KYC<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Verifikasi mitra (KYC)</h1><p class="kq-lead">Tinjau identitas dan rekening mitra. Hanya mitra terverifikasi yang kosnya tampil di pencarian.</p>
<?php if ($mitraList): ?>
<div class="row g-3">
    <?php foreach ($mitraList as $m): ?>
    <div class="col-md-6"><div class="kq-card h-100">
        <div class="d-flex justify-content-between gap-2"><h2 class="h5"><?= esc($m['nama_user']) ?></h2><span class="kq-badge <?= badge_status($m['status_kyc']) ?>"><?= esc($m['status_kyc']) ?></span></div>
        <div class="small text-secondary mb-2"><?= esc($m['email']) ?> · <?= esc($m['no_telp']) ?> · <?= esc($m['kd_mitra']) ?></div>
        <p class="mb-1">No. KTP: <b><?= esc($m['no_ktp']) ?></b></p>
        <p class="mb-2">Rekening: <b><?= esc($m['nama_bank']) ?> <?= esc($m['no_rekening']) ?></b> a.n. <?= esc($m['atas_nama']) ?></p>
        <?php if (! empty($m['foto_ktp'])): ?><a class="btn btn-outline-secondary btn-sm mb-3" target="_blank" rel="noopener" href="<?= base_url('uploads/ktp/' . rawurlencode($m['foto_ktp'])) ?>"><i class="bi bi-person-vcard"></i> Lihat foto KTP</a><?php endif ?>
        <div class="d-flex gap-2 table-actions">
            <?php if ($m['status_kyc'] !== 'verified'): ?><form action="<?= site_url('/admin/kyc/' . rawurlencode($m['kd_mitra']) . '/verified') ?>" method="post"><?= csrf_field() ?><button class="btn btn-success btn-sm" type="submit">Verifikasi</button></form><?php endif ?>
            <?php if ($m['status_kyc'] !== 'rejected'): ?><form action="<?= site_url('/admin/kyc/' . rawurlencode($m['kd_mitra']) . '/rejected') ?>" method="post"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm" type="submit">Tolak</button></form><?php endif ?>
        </div>
    </div></div>
    <?php endforeach ?>
</div>
<?php else: ?><div class="kq-empty">Belum ada pengajuan KYC.</div><?php endif ?>
<?= $this->endSection() ?>
