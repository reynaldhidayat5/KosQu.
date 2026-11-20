<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Cari Kos<?= $this->endSection() ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
<?= $this->endSection() ?>

<?php
$jumlahFilter = count((array) $fasilitas) + (($maxHarga !== null && $maxHarga !== '' && (int) $maxHarga > 0) ? 1 : 0);
$labelTipe = ['putra' => 'Putra', 'putri' => 'Putri', 'campur' => 'Campur'];
?>

<?= $this->section('hero') ?>
<div class="kq-hero">
    <form id="formCari" action="<?= site_url('/') ?>" method="get" class="kq-search">
        <label class="kq-field kq-grow">
            <i class="bi bi-geo-alt-fill"></i>
            <input type="text" name="keyword" value="<?= esc($keyword) ?>" placeholder="Cari kota, daerah, atau nama kos" aria-label="Lokasi">
        </label>
        <label class="kq-field">
            <i class="bi bi-calendar3"></i>
            <input type="date" name="tgl" value="<?= esc($tgl) ?>" min="<?= date('Y-m-d') ?>" aria-label="Tanggal masuk">
        </label>
        <label class="kq-field">
            <i class="bi bi-house-door"></i>
            <select name="tipe" aria-label="Tipe kos">
                <option value="">Semua tipe</option>
                <?php foreach ($labelTipe as $v => $l): ?><option value="<?= $v ?>" <?= $tipe === $v ? 'selected' : '' ?>><?= $l ?></option><?php endforeach ?>
            </select>
        </label>
        <button type="button" class="kq-filter-btn" data-bs-toggle="modal" data-bs-target="#modalFilter" aria-label="Filter">
            <i class="bi bi-sliders fs-5"></i>
            <?php if ($jumlahFilter): ?><span class="n"><?= $jumlahFilter ?></span><?php endif ?>
        </button>
        <button type="submit" class="kq-go"><i class="bi bi-search"></i> Cari Kos</button>

        <!-- Filter lanjutan: harga & fasilitas (Activity Diagram Hal. 5) -->
        <div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content" style="border-radius:1rem">
                <div class="modal-header border-0"><h2 class="modal-title fs-5 fw-bold">Filter pencarian</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body">
                    <label class="form-label fw-semibold" for="max_harga">Harga maksimal per bulan</label>
                    <select id="max_harga" name="max_harga" class="form-select mb-4">
                        <option value="">Semua harga</option>
                        <?php foreach ([500000, 1000000, 1500000, 2000000, 3000000, 5000000] as $h): ?>
                            <option value="<?= $h ?>" <?= (int) $maxHarga === $h ? 'selected' : '' ?>>Sampai <?= rupiah($h) ?></option>
                        <?php endforeach ?>
                    </select>
                    <div class="form-label fw-semibold">Fasilitas</div>
                    <div class="row g-2">
                        <?php foreach ($daftarFas as $f): ?>
                            <div class="col-6"><div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fasilitas[]" id="f_<?= esc($f['kd_fas']) ?>" value="<?= esc($f['kd_fas']) ?>" <?= in_array($f['kd_fas'], (array) $fasilitas, true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="f_<?= esc($f['kd_fas']) ?>"><?= esc(trim(($f['ikon_fas'] ?? '') . ' ' . $f['nm_fas'])) ?></label>
                            </div></div>
                        <?php endforeach ?>
                        <?php if (! $daftarFas): ?><div class="col-12 text-secondary small">Belum ada data fasilitas.</div><?php endif ?>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <a class="btn btn-link text-secondary" href="<?= site_url('/') ?>">Reset</a>
                    <button type="submit" class="btn btn-primary px-4">Terapkan</button>
                </div>
            </div></div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$pins = [];
foreach ($kos as $k) {
    if (is_numeric($k['latitude']) && is_numeric($k['longitude']) && $k['harga_mulai']) {
        $pins[] = [
            'kd' => $k['kd_kos'], 'nm' => $k['nm_kos'], 'lat' => (float) $k['latitude'], 'lng' => (float) $k['longitude'],
            'harga' => rupiah_ringkas($k['harga_mulai']), 'full' => rupiah($k['harga_mulai']),
            'tipe' => $labelTipe[$k['tipe_kos']] ?? '', 'kota' => $k['kota'],
            'foto' => foto_kos_url($k['foto_utama']), 'url' => site_url('kos/detail/' . rawurlencode($k['kd_kos'])) . ($tgl ? '?tgl=' . rawurlencode($tgl) : ''),
        ];
    }
}
?>
<div class="kq-split">
    <section>
        <h2 class="kq-results-title">
            Hasil pencarian <?= $keyword !== '' ? 'untuk “' . esc($keyword) . '”' : 'kos' ?>
            <span class="text-secondary fw-normal">· <span id="jmlHasil"><?= count($kos) ?></span> kos</span>
        </h2>
        <div class="kq-list" id="daftarKos">
            <?php foreach ($kos as $k): ?>
                <?php $tersedia = (int) $k['total_tersedia']; ?>
                <a class="kq-kos" href="<?= site_url('kos/detail/' . rawurlencode($k['kd_kos'])) . ($tgl ? '?tgl=' . rawurlencode($tgl) : '') ?>"
                   data-kd="<?= esc($k['kd_kos']) ?>" data-lat="<?= esc($k['latitude'] ?? '') ?>" data-lng="<?= esc($k['longitude'] ?? '') ?>">
                    <div class="kq-thumb">
                        <img src="<?= esc(foto_kos_url($k['foto_utama'])) ?>" alt="<?= esc($k['nm_kos']) ?>" loading="lazy">
                        <span class="kq-tag <?= esc($k['tipe_kos']) ?>"><?= esc($labelTipe[$k['tipe_kos']] ?? '') ?></span>
                        <button type="button" class="kq-heart" data-fav="<?= esc($k['kd_kos']) ?>" aria-label="Simpan"><i class="bi bi-heart"></i></button>
                        <?php if ($tersedia <= 0): ?><span class="kq-sisa penuh">Penuh</span>
                        <?php elseif ($tersedia <= 3): ?><span class="kq-sisa">Sisa <?= $tersedia ?> kamar</span><?php endif ?>
                    </div>
                    <div class="kq-meta"><i class="bi bi-house-door"></i> Kos · <?= esc($labelTipe[$k['tipe_kos']] ?? '') ?>
                        <?php if ($k['rating_avg']): ?><span class="ms-auto kq-rate"><i class="bi bi-star-fill"></i> <?= esc($k['rating_avg']) ?></span><?php endif ?>
                    </div>
                    <h3 class="kq-name"><?= esc($k['nm_kos']) ?></h3>
                    <div class="kq-sub"><i class="bi bi-geo-alt"></i> <span><?= esc($k['kota']) ?></span></div>
                    <div class="kq-sub"><i class="bi bi-pin-map"></i> <span><?= esc(mb_strimwidth((string) $k['alamat_kos'], 0, 42, '…')) ?></span></div>
                    <?php if ($k['harga_mulai']): ?>
                        <div class="kq-price"><small>mulai dari</small> <?= rupiah($k['harga_mulai']) ?> <small>/bulan</small></div>
                    <?php else: ?>
                        <div class="kq-price"><small>Harga belum diatur</small></div>
                    <?php endif ?>
                    <?php if ($tersedia > 0): ?><span class="kq-chip"><i class="bi bi-check2-circle"></i> <?= $tersedia ?> kamar tersedia</span><?php endif ?>
                </a>
            <?php endforeach ?>
            <div class="kq-empty" id="kosKosong" style="<?= $kos ? 'display:none' : '' ?>">
                <i class="bi bi-search fs-2 d-block mb-2"></i>
                <b>Kos tidak ditemukan</b><br>Coba kata kunci lain atau longgarkan filter pencarian.
            </div>
        </div>
    </section>

    <aside class="kq-mapwrap">
        <label class="kq-mapctl"><input type="checkbox" class="form-check-input mt-0" id="cariGeser"> Cari dengan menggeser peta</label>
        <div id="kqMap"></div>
    </aside>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const PINS = <?= json_encode($pins, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
    const cards = [...document.querySelectorAll('#daftarKos .kq-kos')];
    const empty = document.getElementById('kosKosong');
    const jml = document.getElementById('jmlHasil');

    // Simpan (favorit) - disimpan di browser
    const favKey = 'kosqu_fav';
    let fav = [];
    try { fav = JSON.parse(localStorage.getItem(favKey) || '[]'); } catch (e) { fav = []; }
    document.querySelectorAll('[data-fav]').forEach(btn => {
        const paint = () => { const on = fav.includes(btn.dataset.fav); btn.classList.toggle('on', on); btn.firstElementChild.className = 'bi ' + (on ? 'bi-heart-fill' : 'bi-heart'); };
        paint();
        btn.addEventListener('click', ev => {
            ev.preventDefault(); ev.stopPropagation();
            fav = fav.includes(btn.dataset.fav) ? fav.filter(x => x !== btn.dataset.fav) : fav.concat(btn.dataset.fav);
            try { localStorage.setItem(favKey, JSON.stringify(fav)); } catch (e) {}
            paint();
        });
    });

    // Peta (Leaflet + OpenStreetMap)
    const map = L.map('kqMap', { scrollWheelZoom: true }).setView([-6.9175, 107.6191], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);

    const markers = {};
    PINS.forEach(p => {
        const m = L.marker([p.lat, p.lng], { icon: L.divIcon({ className: '', html: '<span class="kq-pin">' + p.harga + '</span>', iconSize: [0, 0] }) }).addTo(map);
        const pop = document.createElement('div');
        pop.className = 'kq-pop';
        const img = document.createElement('img'); img.src = p.foto; img.alt = p.nm;
        const a = document.createElement('a'); a.href = p.url;
        const b = document.createElement('b'); b.textContent = p.nm;
        const s = document.createElement('small'); s.textContent = p.tipe + ' · ' + p.kota;
        const h = document.createElement('small'); h.textContent = 'mulai dari ' + p.full + ' /bulan';
        a.append(img, b); pop.append(a, s, h);
        m.bindPopup(pop);
        m.on('popupopen', () => setOn(p.kd, true));
        m.on('popupclose', () => setOn(p.kd, false));
        markers[p.kd] = m;
    });
    if (PINS.length > 1) map.fitBounds(PINS.map(p => [p.lat, p.lng]), { padding: [40, 40], maxZoom: 15 });
    else if (PINS.length === 1) map.setView([PINS[0].lat, PINS[0].lng], 15);

    function setOn(kd, on) {
        const m = markers[kd]; if (!m) return;
        const el = m.getElement() && m.getElement().firstElementChild;
        if (el) el.classList.toggle('on', on);
        m.setZIndexOffset(on ? 1000 : 0);
        const c = cards.find(c => c.dataset.kd === kd); if (c) c.classList.toggle('hl', on);
    }
    cards.forEach(c => {
        c.addEventListener('mouseenter', () => setOn(c.dataset.kd, true));
        c.addEventListener('mouseleave', () => setOn(c.dataset.kd, false));
    });

    // "Cari dengan menggeser peta": hanya tampilkan kos yang berada di area peta
    const geser = document.getElementById('cariGeser');
    function terapkan() {
        const b = map.getBounds();
        let n = 0;
        cards.forEach(c => {
            const lat = parseFloat(c.dataset.lat), lng = parseFloat(c.dataset.lng);
            const tampil = !geser.checked || isNaN(lat) || isNaN(lng) || b.contains([lat, lng]);
            c.style.display = tampil ? '' : 'none';
            if (tampil) n++;
        });
        jml.textContent = n;
        empty.style.display = n === 0 ? '' : 'none';
    }
    geser.addEventListener('change', terapkan);
    map.on('moveend', () => { if (geser.checked) terapkan(); });
})();
</script>
<?= $this->endSection() ?>
