<div class="kq-card">
    <div class="d-flex justify-content-between"><span class="text-secondary small">No. pesanan</span><b class="small"><?= esc($p['no_pesan']) ?></b></div>
    <h2 class="h5 mt-2 mb-0"><?= esc($p['nm_kos']) ?></h2>
    <p class="text-secondary small mb-3"><?= esc($p['nm_tipe']) ?> · mulai <?= esc(date('d M Y', strtotime($p['tgl_mulai']))) ?> · <?= (int) $p['durasi'] ?> <?= ['harian' => 'hari', 'bulanan' => 'bulan', 'tahunan' => 'tahun'][$p['satuan_durasi']] ?? '' ?></p>
    <div class="kq-summary">
        <div class="r"><span>Sub total</span><b><?= rupiah($p['sub_total']) ?></b></div>
        <div class="r"><span>Biaya layanan</span><b><?= rupiah($p['biaya_layanan']) ?></b></div>
        <div class="r t"><span>Total bayar</span><span><?= rupiah($p['total_bayar']) ?></span></div>
    </div>
</div>
