<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Payout Mitra<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $pct = rtrim(rtrim(number_format($komisi, 2, ',', ''), '0'), ','); ?>
<h1 class="kq-h1">Payout mitra</h1><p class="kq-lead">Tinjau pengajuan penarikan. Komisi platform saat ini <b><?= $pct ?>%</b> (<a href="<?= site_url('/admin/pengaturan') ?>">ubah</a>).</p>
<?php if ($payoutList): ?>
<div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
    <thead><tr><th>Kode</th><th>Mitra</th><th>Rekening tujuan</th><th>Diajukan</th><th>Jumlah</th><th>Komisi</th><th>Bersih</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody><?php foreach ($payoutList as $p): $diajukan = $p['status_tarik'] === 'Diajukan'; $k = (int) round($p['jumlah_tarik'] * $komisi / 100); ?>
        <tr><td><?= esc($p['kd_tarik']) ?></td><td class="fw-semibold"><?= esc($p['nama_user']) ?></td><td><?= esc($p['nama_bank']) ?> <?= esc($p['no_rekening']) ?><div class="small text-secondary">a.n. <?= esc($p['atas_nama']) ?></div></td>
            <td><?= esc(date('d M Y', strtotime($p['tgl_ajuan']))) ?></td><td><?= rupiah($p['jumlah_tarik']) ?></td>
            <td><?= $diajukan ? '<span class="text-secondary">~' . rupiah($k) . '</span>' : ($p['status_tarik'] === 'Dicairkan' ? rupiah($p['komisi']) : '—') ?></td>
            <td><?= $diajukan ? '<span class="text-secondary">~' . rupiah($p['jumlah_tarik'] - $k) . '</span>' : ($p['status_tarik'] === 'Dicairkan' ? rupiah($p['jumlah_bersih']) : '—') ?></td>
            <td><span class="kq-badge <?= badge_status($p['status_tarik']) ?>"><?= esc($p['status_tarik']) ?></span></td>
            <td class="text-nowrap table-actions"><?php if ($diajukan): ?>
                <form action="<?= site_url('/admin/payout/' . rawurlencode($p['kd_tarik']) . '/setujui') ?>" method="post" onsubmit="return confirm('Setujui & cairkan dana ke rekening mitra?')"><?= csrf_field() ?><button class="btn btn-success btn-sm" type="submit">Setujui</button></form>
                <form action="<?= site_url('/admin/payout/' . rawurlencode($p['kd_tarik']) . '/tolak') ?>" method="post" onsubmit="return confirm('Tolak pengajuan? Saldo dikembalikan ke mitra.')"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm" type="submit">Tolak</button></form>
            <?php else: ?><span class="text-secondary small"><?= esc($p['tgl_proses'] ?? '—') ?></span><?php endif ?></td></tr>
    <?php endforeach ?></tbody>
</table></div></div>
<?php else: ?><div class="kq-empty">Belum ada pengajuan payout.</div><?php endif ?>
<?= $this->endSection() ?>
