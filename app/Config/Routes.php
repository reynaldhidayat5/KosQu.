<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ---------------------------------------------------------------
// Publik: beranda + pencarian kos + detail kos
// ---------------------------------------------------------------
$routes->get('/', 'Customer\KosController::index');
$routes->get('kos/detail/(:segment)', 'Customer\KosController::detail/$1');

// ---------------------------------------------------------------
// Auth (Login / Registrasi / Logout)
// ---------------------------------------------------------------
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::storeRegister');
$routes->get('logout', 'AuthController::logout');

// ---------------------------------------------------------------
// Akun (semua role yang sudah login) - Menu akun / ubah profil
// ---------------------------------------------------------------
$routes->group('akun', ['filter' => 'role:customer,mitra,admin'], static function ($routes) {
    $routes->get('/', 'AkunController::index');
    $routes->post('simpan', 'AkunController::simpan');
});

// ---------------------------------------------------------------
// Customer (Pencari Kos)
// ---------------------------------------------------------------
$routes->group('customer', ['filter' => 'role:customer'], static function ($routes) {
    // Pemesanan
    $routes->get('pesan/(:segment)', 'Customer\PemesananController::form/$1');
    $routes->post('pesan', 'Customer\PemesananController::buat');
    $routes->post('batal/(:segment)', 'Customer\PemesananController::batalkan/$1');

    // Riwayat, invoice, review
    $routes->get('riwayat', 'Customer\PemesananController::riwayat');
    $routes->get('invoice/(:segment)', 'Customer\PemesananController::invoice/$1');
    $routes->post('review/(:segment)', 'Customer\PemesananController::review/$1');

    // Pembayaran
    $routes->get('bayar/(:segment)', 'Customer\PembayaranController::form/$1');
    $routes->post('bayar/(:segment)/metode', 'Customer\PembayaranController::pilihMetode/$1');
    $routes->get('bayar/(:segment)/instruksi', 'Customer\PembayaranController::instruksi/$1');
    $routes->post('bayar/(:segment)/bukti', 'Customer\PembayaranController::unggahBukti/$1');
    $routes->post('bayar/(:segment)/simulasi', 'Customer\PembayaranController::simulasiGateway/$1');
});

// ---------------------------------------------------------------
// Mitra (Pemilik Kos)
// ---------------------------------------------------------------
$routes->group('mitra', ['filter' => 'role:mitra'], static function ($routes) {
    $routes->get('dashboard', 'Mitra\MitraController::dashboard');
    $routes->get('laporan', 'Mitra\MitraController::laporan');

    // Master: data kos
    $routes->get('kos', 'Mitra\KosController::index');
    $routes->get('kos/edit/(:segment)', 'Mitra\KosController::index/$1');
    $routes->post('kos/simpan', 'Mitra\KosController::simpan');
    $routes->post('kos/hapus/(:segment)', 'Mitra\KosController::hapus/$1');
    $routes->post('kos/foto/hapus/(:segment)', 'Mitra\KosController::hapusFoto/$1');

    // Master: tipe kamar, harga & ketersediaan
    $routes->get('kamar', 'Mitra\KamarController::index');
    $routes->get('kamar/edit/(:segment)', 'Mitra\KamarController::index/$1');
    $routes->post('kamar/simpan', 'Mitra\KamarController::simpan');
    $routes->post('kamar/hapus/(:segment)', 'Mitra\KamarController::hapus/$1');

    // Pesanan masuk
    $routes->get('pesanan', 'Mitra\PesananController::index');
    $routes->post('pesanan/verifikasi/(:segment)/(:alpha)', 'Mitra\PesananController::verifikasi/$1/$2');

    // Penarikan dana
    $routes->get('penarikan', 'Mitra\PayoutController::index');
    $routes->post('payout/ajukan', 'Mitra\PayoutController::ajukan');
});

// ---------------------------------------------------------------
// Admin Utama (Superadmin)
// ---------------------------------------------------------------
$routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('dashboard', 'Admin\AdminController::dashboard');

    // Master data
    $routes->get('user', 'Admin\AdminController::user');
    $routes->post('user/status/(:segment)', 'Admin\AdminController::statusUser/$1');
    $routes->get('mitra', 'Admin\AdminController::mitra');
    $routes->get('kos', 'Admin\AdminController::kos');
    $routes->post('kos/status/(:segment)', 'Admin\AdminController::statusKos/$1');
    $routes->get('fasilitas', 'Admin\AdminController::fasilitas');
    $routes->get('fasilitas/edit/(:segment)', 'Admin\AdminController::fasilitas/$1');
    $routes->post('fasilitas/simpan', 'Admin\AdminController::simpanFasilitas');
    $routes->post('fasilitas/hapus/(:segment)', 'Admin\AdminController::hapusFasilitas/$1');

    // Verifikasi mitra (KYC)
    $routes->get('kyc', 'Admin\AdminController::kyc');
    $routes->post('kyc/(:segment)/(:alpha)', 'Admin\AdminController::verifikasiKyc/$1/$2');

    // Transaksi & pembayaran
    $routes->get('transaksi', 'Admin\AdminController::transaksi');
    $routes->get('pembayaran', 'Admin\AdminController::pembayaran');

    // Payout & komisi
    $routes->get('payout', 'Admin\PayoutController::index');
    $routes->post('payout/(:segment)/(:alpha)', 'Admin\PayoutController::proses/$1/$2');
    $routes->get('pengaturan', 'Admin\AdminController::pengaturan');
    $routes->post('pengaturan/simpan', 'Admin\AdminController::simpanPengaturan');

    // Laporan
    $routes->get('laporan', 'Admin\AdminController::laporan');
});
