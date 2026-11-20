<?php
$role   = session()->get('role');
$login  = (bool) session()->get('isLoggedIn');
$path   = '/' . trim(uri_string(), '/');

// [label, url] atau [label, [[label, url], ...]] (dropdown)
$menus = [
    'customer' => [
        ['Cari Kos', '/'],
        ['Riwayat Pesanan', '/customer/riwayat'],
    ],
    'mitra' => [
        ['Dashboard', '/mitra/dashboard'],
        ['Master', [['Data Kos', '/mitra/kos'], ['Tipe Kamar & Harga', '/mitra/kamar']]],
        ['Pesanan', '/mitra/pesanan'],
        ['Laporan', '/mitra/laporan'],
        ['Penarikan Dana', '/mitra/penarikan'],
    ],
    'admin' => [
        ['Dashboard', '/admin/dashboard'],
        ['Master', [['Data User', '/admin/user'], ['Data Mitra', '/admin/mitra'], ['Data Kos', '/admin/kos'], ['Data Fasilitas', '/admin/fasilitas']]],
        ['Verifikasi KYC', '/admin/kyc'],
        ['Transaksi', [['Transaksi Global', '/admin/transaksi'], ['Data Pembayaran', '/admin/pembayaran']]],
        ['Payout', [['Penarikan Dana', '/admin/payout'], ['Komisi Platform', '/admin/pengaturan']]],
        ['Laporan', '/admin/laporan'],
    ],
];
$menu = $menus[$role] ?? [['Cari Kos', '/']];
$aktif = static fn (string $url): string => ($url === '/' ? $path === '/' : str_starts_with($path, $url)) ? 'active' : '';
$errors = session()->getFlashdata('errors');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc(trim($this->renderSection('title')) ?: 'KosQu') ?> | KosQu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/kosqu.css') ?>" rel="stylesheet">
    <?= $this->renderSection('head') ?>
</head>
<body>
<header class="kq-header">
    <div class="container-xl">
        <div class="kq-nav">
            <a class="kq-brand" href="<?= site_url('/') ?>"><span class="dot"><i class="bi bi-house-heart-fill"></i></span>KosQu</a>
            <ul class="kq-menu">
                <?php foreach ($menu as [$label, $target]): ?>
                    <?php if (is_array($target)): ?>
                        <li class="dropdown">
                            <button class="dropdown-toggle" data-bs-toggle="dropdown" type="button"><?= esc($label) ?></button>
                            <ul class="dropdown-menu">
                                <?php foreach ($target as [$l2, $u2]): ?>
                                    <li><a class="dropdown-item" href="<?= site_url($u2) ?>"><?= esc($l2) ?></a></li>
                                <?php endforeach ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a class="<?= $aktif($target) ?>" href="<?= site_url($target) ?>"><?= esc($label) ?></a></li>
                    <?php endif ?>
                <?php endforeach ?>
            </ul>
            <div class="kq-right">
                <?php if ($login): ?>
                    <div class="dropdown">
                        <a class="kq-user dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <span class="kq-avatar"><?= esc(strtoupper(mb_substr((string) session()->get('nama_user'), 0, 1))) ?></span>
                            <?= esc(session()->get('nama_user')) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-secondary text-capitalize"><?= esc($role) ?></span></li>
                            <li><a class="dropdown-item" href="<?= site_url('/akun') ?>">Akun saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= site_url('/logout') ?>">Keluar</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="kq-user" href="<?= site_url('/login') ?>"><i class="bi bi-person-fill"></i> Masuk / Daftar</a>
                <?php endif ?>
            </div>
        </div>
        <?= $this->renderSection('hero') ?>
    </div>
</header>

<main class="container-xl kq-page">
    <?php foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $key => $style): ?>
        <?php if ($msg = session()->getFlashdata($key)): ?>
            <div class="alert alert-<?= $style ?> kq-flash alert-dismissible fade show" role="alert">
                <?= esc($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif ?>
    <?php endforeach ?>
    <?php if (! empty($errors) && is_array($errors)): ?>
        <div class="alert alert-danger kq-flash">
            <ul class="mb-0 ps-3"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>
    <?= $this->renderSection('content') ?>
</main>

<footer class="kq-footer">
    <div class="container-xl d-flex flex-wrap justify-content-between gap-2">
        <span>&copy; <?= date('Y') ?> KosQu &middot; Platform pencarian dan pemesanan kos</span>
        <span>Temukan hunian yang tepat untukmu</span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
