<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Pemesanan<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$tglAwal = old('tgl_mulai') ?? ($tgl && $tgl >= date('Y-m-d') ? esc($tgl) : date('Y-m-d'));
$satuan  = old('satuan_durasi') ?? 'bulanan';
?>
<nav class="small mb-3"><a href="<?= site_url('kos/detail/' . rawurlencode($tipe['kd_kos'])) ?>"><i class="bi bi-arrow-left"></i> Kembali ke detail kos</a></nav>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="kq-card p-4">
            <h1 class="kq-h1">Form pemesanan</h1>
            <p class="kq-lead">Tentukan tanggal masuk dan durasi sewa. Total akan dihitung otomatis.</p>
            <form action="<?= site_url('/customer/pesan') ?>" method="post" id="formPesan">
                <?= csrf_field() ?>
                <input type="hidden" name="kd_tipe" value="<?= esc($tipe['kd_tipe']) ?>">
                <div class="mb-3"><label class="form-label fw-semibold">Tipe kamar</label>
                    <input class="form-control" value="<?= esc($tipe['nm_tipe'] . ' — ' . $tipe['nm_kos']) ?>" disabled></div>
                <div class="mb-3"><label for="tgl_mulai" class="form-label fw-semibold">Tanggal masuk</label>
                    <input id="tgl_mulai" name="tgl_mulai" type="date" class="form-control" min="<?= date('Y-m-d') ?>" value="<?= $tglAwal ?>" required></div>
                <div class="row g-3">
                    <div class="col-sm-6"><label for="durasi" class="form-label fw-semibold">Durasi sewa</label>
                        <input id="durasi" name="durasi" type="number" min="1" max="365" value="<?= old('durasi') ?? 1 ?>" class="form-control" required></div>
                    <div class="col-sm-6"><label for="satuan_durasi" class="form-label fw-semibold">Satuan</label>
                        <select id="satuan_durasi" name="satuan_durasi" class="form-select">
                            <option value="harian" <?= $satuan === 'harian' ? 'selected' : '' ?>>Hari</option>
                            <option value="bulanan" <?= $satuan === 'bulanan' ? 'selected' : '' ?>>Bulan</option>
                            <option value="tahunan" <?= $satuan === 'tahunan' ? 'selected' : '' ?>>Tahun</option>
                        </select></div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">Konfirmasi pesanan</button>
                <div class="form-text text-center">Setelah dikonfirmasi, Anda punya waktu 24 jam untuk membayar.</div>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="kq-card kq-sticky">
            <h2 class="h5"><?= esc($tipe['nm_kos']) ?></h2>
            <p class="text-secondary small"><i class="bi bi-geo-alt"></i> <?= esc($tipe['alamat_kos']) ?>, <?= esc($tipe['kota']) ?></p>
            <div class="kq-summary">
                <div class="r"><span id="rHarga">Harga sewa</span><b id="vHarga">-</b></div>
                <div class="r"><span>Sub total</span><b id="vSub">-</b></div>
                <div class="r"><span>Biaya layanan</span><b id="vFee"><?= rupiah($biayaLayanan) ?></b></div>
                <div class="r t"><span>Total bayar</span><span id="vTotal">-</span></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
(function () {
    const H = { harian: <?= (int) $tipe['harga_harian'] ?>, bulanan: <?= (int) $tipe['harga_bulanan'] ?>, tahunan: <?= (int) $tipe['harga_tahunan'] ?> };
    const FEE = <?= (int) $biayaLayanan ?>;
    const NAMA = { harian: 'hari', bulanan: 'bulan', tahunan: 'tahun' };
    const rp = n => 'Rp' + Math.round(n).toLocaleString('id-ID');
    const d = document.getElementById('durasi'), s = document.getElementById('satuan_durasi');
    function hitung() {
        const harga = H[s.value] || 0, n = Math.max(1, parseInt(d.value || '1', 10));
        const sub = harga * n;
        document.getElementById('rHarga').textContent = 'Harga per ' + NAMA[s.value];
        document.getElementById('vHarga').textContent = harga ? rp(harga) : 'Belum diatur';
        document.getElementById('vSub').textContent = rp(sub);
        document.getElementById('vTotal').textContent = rp(sub + FEE);
    }
    d.addEventListener('input', hitung); s.addEventListener('change', hitung); hitung();
})();
</script>
<?= $this->endSection() ?>
