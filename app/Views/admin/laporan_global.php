<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Laporan Global<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="kq-h1">Laporan transaksi global</h1><p class="kq-lead">Berdasarkan invoice yang terbit pada periode terpilih.</p>
<form method="get" action="<?= site_url('/admin/laporan') ?>" class="row g-2 align-items-end mb-4">
    <div class="col-sm-4 col-md-3"><label class="form-label fw-semibold small" for="dari">Dari tanggal</label><input id="dari" type="date" name="dari" class="form-control" value="<?= esc($dari) ?>"></div>
    <div class="col-sm-4 col-md-3"><label class="form-label fw-semibold small" for="sampai">Sampai tanggal</label><input id="sampai" type="date" name="sampai" class="form-control" value="<?= esc($sampai) ?>"></div>
    <div class="col-auto"><button class="btn btn-primary" type="submit">Terapkan</button> <a class="btn btn-outline-secondary" href="<?= site_url('/admin/laporan') ?>">Reset</a> <button class="btn btn-outline-primary no-print" type="button" onclick="window.print()">Cetak</button></div>
</form>
<div class="row g-3 mb-4">
    <?php foreach (['total_pesanan' => ['Pesanan berhasil', false], 'total_transaksi' => ['Nilai transaksi', true], 'biaya_layanan' => ['Biaya layanan', true], 'komisi' => ['Komisi payout (total)', true], 'total_user' => ['Pengguna', false], 'total_kos' => ['Kos', false]] as $k => [$l, $rp]): ?>
        <div class="col-6 col-lg-2"><div class="kq-stat"><div class="l"><?= $l ?></div><div class="v fs-4"><?= $rp ? rupiah($laporan[$k]) : number_format((float) $laporan[$k], 0, ',', '.') ?></div></div></div>
    <?php endforeach ?>
</div>
<div class="kq-card p-0"><div class="table-responsive"><table class="kq-table">
    <thead><tr><th>Tanggal</th><th>Invoice</th><th>Customer</th><th>Kos</th><th class="text-end">Sub total</th><th class="text-end">Layanan</th><th class="text-end">Total</th></tr></thead>
    <tbody><?php foreach ($transaksi as $t): ?>
        <tr><td><?= esc(date('d M Y', strtotime($t['tgl_terbit']))) ?></td><td><?= esc($t['no_invoice']) ?></td><td><?= esc($t['nama_user']) ?></td><td><?= esc($t['nm_kos']) ?></td><td class="text-end"><?= rupiah($t['sub_total']) ?></td><td class="text-end"><?= rupiah($t['biaya_layanan']) ?></td><td class="text-end fw-semibold"><?= rupiah($t['total_bayar']) ?></td></tr>
    <?php endforeach ?><?php if (! $transaksi): ?><tr><td colspan="7" class="text-center text-secondary py-4">Tidak ada transaksi pada periode ini.</td></tr><?php endif ?></tbody>
</table></div></div>
<?= $this->endSection() ?>
