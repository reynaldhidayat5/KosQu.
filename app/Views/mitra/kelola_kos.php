<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Data Kos<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php $edit = (bool) $kos; ?>
<h1 class="kq-h1">Data kos</h1>
<p class="kq-lead">Tambah, cari, ubah, atau hapus properti kos Anda.</p>
<?php if ($mitra['status_kyc'] !== 'verified'): ?>
    <div class="alert alert-warning kq-flash">KYC Anda belum terverifikasi. Anda belum dapat menyimpan data kos.</div>
<?php endif ?>
<div class="row g-4">
    <div class="col-lg-5">
        <form action="<?= site_url('/mitra/kos/simpan') ?>" method="post" enctype="multipart/form-data" class="kq-card">
            <?= csrf_field() ?>
            <input type="hidden" name="kd_kos" value="<?= esc($kos['kd_kos'] ?? '') ?>">
            <h2 class="h5 mb-3"><?= $edit ? 'Ubah kos' : 'Tambah kos baru' ?></h2>
            <div class="mb-3"><label for="nm_kos" class="form-label fw-semibold">Nama kos</label><input id="nm_kos" name="nm_kos" class="form-control" maxlength="50" value="<?= nilai('nm_kos', $kos['nm_kos'] ?? '') ?>" required></div>
            <div class="mb-3"><label for="alamat_kos" class="form-label fw-semibold">Alamat</label><textarea id="alamat_kos" name="alamat_kos" class="form-control" rows="2" required><?= nilai('alamat_kos', $kos['alamat_kos'] ?? '') ?></textarea></div>
            <div class="row g-3 mb-3">
                <div class="col-6"><label for="kota" class="form-label fw-semibold">Kota</label><input id="kota" name="kota" class="form-control" maxlength="30" value="<?= nilai('kota', $kos['kota'] ?? '') ?>" required></div>
                <div class="col-6"><label for="tipe_kos" class="form-label fw-semibold">Tipe kos</label>
                    <?php $tp = old('tipe_kos') ?? ($kos['tipe_kos'] ?? 'campur'); ?>
                    <select id="tipe_kos" name="tipe_kos" class="form-select"><?php foreach (['putra' => 'Putra', 'putri' => 'Putri', 'campur' => 'Campur'] as $k => $l): ?><option value="<?= $k ?>" <?= $tp === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach ?></select></div>
                <div class="col-6"><label for="latitude" class="form-label fw-semibold">Latitude</label><input id="latitude" name="latitude" class="form-control" placeholder="-6.8915" value="<?= nilai('latitude', $kos['latitude'] ?? '') ?>"></div>
                <div class="col-6"><label for="longitude" class="form-label fw-semibold">Longitude</label><input id="longitude" name="longitude" class="form-control" placeholder="107.6107" value="<?= nilai('longitude', $kos['longitude'] ?? '') ?>"></div>
                <div class="col-12 form-text mt-0">Titik peta: klik kanan lokasi di Google Maps lalu salin koordinatnya.</div>
            </div>
            <div class="mb-3"><label for="status_kos" class="form-label fw-semibold">Status tayang</label>
                <?php $st = old('status_kos') ?? ($kos['status_kos'] ?? 'aktif'); ?>
                <select id="status_kos" name="status_kos" class="form-select"><option value="aktif" <?= $st === 'aktif' ? 'selected' : '' ?>>Aktif (tampil di pencarian)</option><option value="nonaktif" <?= $st === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option></select></div>
            <div class="mb-3"><label for="deskripsi" class="form-label fw-semibold">Deskripsi</label><textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= nilai('deskripsi', $kos['deskripsi'] ?? '') ?></textarea></div>
            <div class="mb-3"><label for="aturan_kos" class="form-label fw-semibold">Aturan kos</label><textarea id="aturan_kos" name="aturan_kos" class="form-control" rows="3"><?= nilai('aturan_kos', $kos['aturan_kos'] ?? '') ?></textarea></div>

            <div class="mb-3">
                <div class="form-label fw-semibold">Fasilitas</div>
                <?php $pilih = (array) (old('fasilitas') ?? $fasilitasKos); ?>
                <div class="row g-1">
                    <?php foreach ($daftarFas as $f): ?>
                        <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="fasilitas[]" id="mf_<?= esc($f['kd_fas']) ?>" value="<?= esc($f['kd_fas']) ?>" <?= in_array($f['kd_fas'], $pilih, true) ? 'checked' : '' ?>><label class="form-check-label" for="mf_<?= esc($f['kd_fas']) ?>"><?= esc(trim(($f['ikon_fas'] ?? '') . ' ' . $f['nm_fas'])) ?></label></div></div>
                    <?php endforeach ?>
                    <?php if (! $daftarFas): ?><div class="col-12 text-secondary small">Admin belum menambahkan data fasilitas.</div><?php endif ?>
                </div>
            </div>

            <div class="mb-3"><label for="foto" class="form-label fw-semibold">Foto kos</label><input id="foto" name="foto[]" type="file" class="form-control" accept="image/*" multiple><div class="form-text">Bisa lebih dari satu, maks. 3 MB per foto. Foto pertama menjadi foto utama.</div></div>
            <?php if ($fotoKos): ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php foreach ($fotoKos as $ft): ?>
                        <div class="position-relative"><img src="<?= esc(foto_kos_url($ft['url_foto'])) ?>" alt="" style="width:84px;height:64px;object-fit:cover;border-radius:.5rem">
                            <button form="hf_<?= esc($ft['kd_foto']) ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0" style="width:1.3rem;height:1.3rem;line-height:1" type="submit" title="Hapus foto">&times;</button></div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>

            <?php if (! $edit): ?>
                <hr><div class="form-label fw-semibold">Tipe kamar awal <span class="text-secondary fw-normal">(opsional)</span></div>
                <div class="row g-2 mb-3">
                    <div class="col-12"><input name="nm_tipe" class="form-control" maxlength="30" placeholder="Nama tipe, mis. Tipe A (AC)" value="<?= nilai('nm_tipe') ?>"></div>
                    <div class="col-6"><input name="jml_kamar" type="number" min="1" class="form-control" placeholder="Jumlah kamar" value="<?= nilai('jml_kamar') ?>"></div>
                    <div class="col-6"><input name="harga_bulanan" type="number" min="0" class="form-control" placeholder="Harga / bulan" value="<?= nilai('harga_bulanan') ?>"></div>
                    <div class="col-6"><input name="harga_harian" type="number" min="0" class="form-control" placeholder="Harga / hari" value="<?= nilai('harga_harian') ?>"></div>
                    <div class="col-6"><input name="harga_tahunan" type="number" min="0" class="form-control" placeholder="Harga / tahun" value="<?= nilai('harga_tahunan') ?>"></div>
                </div>
            <?php endif ?>
            <div class="d-flex gap-2">
                <button class="btn btn-primary px-4" type="submit">Simpan</button>
                <?php if ($edit): ?><a class="btn btn-outline-secondary" href="<?= site_url('/mitra/kos') ?>">Batal</a><?php endif ?>
            </div>
        </form>
        <?php foreach ($fotoKos as $ft): ?>
            <form id="hf_<?= esc($ft['kd_foto']) ?>" action="<?= site_url('/mitra/kos/foto/hapus/' . rawurlencode($ft['kd_foto'])) ?>" method="post"><?= csrf_field() ?></form>
        <?php endforeach ?>
    </div>

    <div class="col-lg-7">
        <div class="kq-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="h5 mb-0">Daftar kos</h2>
                <form method="get" action="<?= site_url('/mitra/kos') ?>" class="d-flex gap-2"><input name="cari" value="<?= esc($cari) ?>" class="form-control form-control-sm" placeholder="Cari nama / kota"><button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button></form>
            </div>
            <?php if ($daftarKos): ?>
                <div class="table-responsive"><table class="kq-table">
                    <thead><tr><th>Nama</th><th>Kota</th><th>Tipe</th><th>Kamar</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($daftarKos as $k): ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($k['nm_kos']) ?></td><td><?= esc($k['kota']) ?></td><td class="text-capitalize"><?= esc($k['tipe_kos']) ?></td>
                            <td><?= (int) $k['jml_tipe'] ?> tipe</td>
                            <td><span class="kq-badge <?= badge_status($k['status_kos']) ?>"><?= esc($k['status_kos']) ?></span></td>
                            <td class="text-nowrap table-actions">
                                <a class="btn btn-outline-primary btn-sm" href="<?= site_url('/mitra/kos/edit/' . rawurlencode($k['kd_kos'])) ?>">Ubah</a>
                                <form action="<?= site_url('/mitra/kos/hapus/' . rawurlencode($k['kd_kos'])) ?>" method="post" onsubmit="return confirm('Hapus kos ini beserta tipe kamarnya?')"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm" type="submit">Hapus</button></form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table></div>
            <?php else: ?><p class="text-secondary mb-0"><?= $cari ? 'Data tidak ditemukan.' : 'Belum ada kos terdaftar.' ?></p><?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
