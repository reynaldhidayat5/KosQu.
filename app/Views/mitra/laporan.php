<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Laporan Keuangan<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Laporan keuangan</h1>
<p class="kq-lead">Pendapatan dari pesanan yang diterima (berdasarkan tanggal terbit invoice).</p>
<form method="get" action="<?= site_url('/mitra/laporan') ?>" class="row g-2 align-items-end mb-4">
    <div class="col-sm-4 col-md-3"><label class="form-label fw-semibold small" for="dari">Dari tanggal</label><input id="dari" type="date" name="dari" class="form-control" value="<?= esc($dari) ?>"></div>
    <div class="col-sm-4 col-md-3"><label class="form-label fw-semibold small" for="sampai">Sampai tanggal</label><input id="sampai" type="date" name="sampai" class="form-control" value="<?= esc($sampai) ?>"></div>
    <div class="col-auto"><button class="btn btn-primary" type="submit">Terapkan</button> <a class="btn btn-outline-secondary" href="<?= site_url('/mitra/laporan') ?>">Reset</a></div>
</form>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="kq-stat"><div class="l">Pesanan diterima</div><div class="v"><?= count($transaksi) ?></div></div></div>
    <div class="col-md-3"><div class="kq-stat"><div class="l">Pendapatan (periode)</div><div class="v"><?= rupiah($pendapatan) ?></div></div></div>
    <div class="col-md-3"><div class="kq-stat"><div class="l">Saldo saat ini</div><div class="v"><?= rupiah($saldo) ?></div></div></div>
    <div class="col-md-3"><div class="kq-stat"><div class="l">Sudah dicairkan</div><div class="v"><?= rupiah($tarik['bersih']) ?></div><div class="small text-secondary">komisi platform <?= rupiah($tarik['komisi']) ?></div></div></div>
</div>
<div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
    <thead><tr><th>Tanggal</th><th>Invoice</th><th>Penyewa</th><th>Kos / tipe</th><th class="text-end">Pendapatan</th></tr></thead>
    <tbody>
    <?php foreach ($transaksi as $t): ?>
        <tr><td><?= esc(date('d M Y', strtotime($t['tgl_terbit']))) ?></td><td><?= esc($t['no_invoice']) ?></td><td><?= esc($t['nama_user']) ?></td><td><?= esc($t['nm_kos']) ?> · <?= esc($t['nm_tipe']) ?></td><td class="text-end fw-semibold"><?= rupiah($t['sub_total']) ?></td></tr>
    <?php endforeach ?>
    <?php if (! $transaksi): ?><tr><td colspan="5" class="text-center text-secondary py-4">Belum ada transaksi pada periode ini.</td></tr><?php endif ?>
    </tbody>
</table></div></div>
<?= $this->endSection() ?>
