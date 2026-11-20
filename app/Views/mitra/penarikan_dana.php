<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Penarikan Dana<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Penarikan dana</h1>
<p class="kq-lead">Ajukan pencairan saldo hasil penyewaan ke rekening Anda.</p>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="kq-card">
            <div class="text-secondary small fw-semibold">Saldo tersedia</div>
            <div class="kq-h1 mt-1"><?= rupiah($saldo) ?></div>
            <div class="small text-secondary mb-3">Rekening: <?= esc($mitra['nama_bank']) ?> <?= esc($mitra['no_rekening']) ?> a.n. <?= esc($mitra['atas_nama']) ?></div>
            <form action="<?= site_url('/mitra/payout/ajukan') ?>" method="post">
                <?= csrf_field() ?>
                <label for="jumlah_tarik" class="form-label fw-semibold">Jumlah penarikan (Rp)</label>
                <input id="jumlah_tarik" name="jumlah_tarik" type="number" min="1" max="<?= (int) $saldo ?>" class="form-control mb-2" required <?= $saldo < 1 ? 'disabled' : '' ?>>
                <div class="form-text mb-3">Komisi platform <?= rtrim(rtrim(number_format($komisi, 2, ',', ''), '0'), ',') ?>% dipotong saat pencairan. Saldo ditahan sampai admin memproses.</div>
                <button class="btn btn-primary w-100" type="submit" <?= $saldo < 1 ? 'disabled' : '' ?>>Ajukan penarikan</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="kq-card">
            <h2 class="h5 mb-3">Riwayat penarikan</h2>
            <?php if ($riwayat): ?>
            <div class="table-responsive"><table class="kq-table">
                <thead><tr><th>Kode</th><th>Diajukan</th><th>Jumlah</th><th>Komisi</th><th>Diterima</th><th>Status</th></tr></thead>
                <tbody><?php foreach ($riwayat as $r): ?>
                    <tr><td><?= esc($r['kd_tarik']) ?></td><td><?= esc(date('d M Y', strtotime($r['tgl_ajuan']))) ?></td><td><?= rupiah($r['jumlah_tarik']) ?></td>
                        <td><?= $r['status_tarik'] === 'Dicairkan' ? rupiah($r['komisi']) : '—' ?></td><td><?= $r['status_tarik'] === 'Dicairkan' ? rupiah($r['jumlah_bersih']) : '—' ?></td>
                        <td><span class="kq-badge <?= badge_status($r['status_tarik']) ?>"><?= esc($r['status_tarik']) ?></span></td></tr>
                <?php endforeach ?></tbody>
            </table></div>
            <?php else: ?><p class="text-secondary mb-0">Belum ada pengajuan penarikan.</p><?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
