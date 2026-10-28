<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

// Password Reset Routes
$routes->get('auth/lupa-password', 'Auth::forgotPassword');
$routes->post('auth/forgotSubmit', 'Auth::forgotSubmit');

$routes->get('test-email', 'Auth::testEmail');

$routes->get('auth/resetPassword/(:any)', 'Auth::resetPassword/$1');
$routes->post('auth/forgotSubmit', 'Auth::forgotSubmit');

// User Password Update Route
$routes->get('user/updatePassword', 'Auth::updatePassword');
$routes->post('auth/updatePasswordSubmit', 'Auth::updatePasswordSubmit');

// Development Login Bypass Route
$routes->get('dev/login/(:any)', 'Auth::loginBypass/$1');

// Route Halaman Admin
$routes->get('admin/dashboard', 'Admin::dashboard');

// ADMIN ROUTES
$routes->get('admin/dashboard', 'Admin::dashboard');
$routes->get('admin/users', 'Admin::manageUsers');
$routes->post('admin/changeRole', 'Admin::changeRole');

// ROUTE KHUSUS FAKULTAS (
$routes->group('fakultas', function ($routes) {
    $routes->get('/', 'Fakultas::dashboard');
    $routes->get('dashboard', 'Fakultas::dashboard');
    $routes->get('repository', 'Fakultas::dashboard');
});

// JALUR UNTUK RESET PASSWORD PAKSA
$routes->post('admin/forceReset', 'Admin::forceResetPassword');


// TENAGA KEPENDIDIKAN ROUTES PEMBIMBING EKSTERNAL
$routes->get('TenagaKependidikan/pembimbingEksternal', 'TenagaKependidikan::pembimbingEksternal');
$routes->post('TenagaKependidikan/uploadDokumen', 'TenagaKependidikan::uploadDokumen');
$routes->get('TenagaKependidikan/hapusDokumen/(:num)/(:segment)', 'TenagaKependidikan::hapusDokumen/$1/$2');

// Dashboard routes


/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Auth::index');


/*
|--------------------------------------------------------------------------
| ROUTE KELOLA MATA KULIAH KAPRODI
|--------------------------------------------------------------------------
*/

// Halaman daftar mata kuliah
$routes->get('Kaprodi/mataKuliah', 'Kaprodi::mataKuliah');

// API GET: mengambil daftar mata kuliah prodi
$routes->get(
    'api/kaprodi/mata-kuliah',
    'Kaprodi::apiMataKuliah'
);

// API POST: menambahkan mata kuliah ke prodi
$routes->post(
    'api/kaprodi/mata-kuliah',
    'Kaprodi::apiSimpanMataKuliah'
);

// Form tambah mata kuliah yang sudah ada
$routes->post(
    'Kaprodi/simpanMataKuliah',
    'Kaprodi::simpanMataKuliah'
);
// Edit mata kuliah
$routes->post(
    'Kaprodi/updateMataKuliah',
    'Kaprodi::updateMataKuliah'
);

// Hapus mata kuliah
$routes->post(
    'Kaprodi/hapusMataKuliah',
    'Kaprodi::hapusMataKuliah'
);


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}




$routes->group('TenagaKependidikan', ['filter' => 'role:tendik'], function($routes) {
    // Rute Halaman Utama (US 13, 14, 15)
    $routes->get('pengajuanSkripsi', 'TenagaKependidikan::pengajuanSkripsi');
    $routes->get('pengajuanPrasidang', 'TenagaKependidikan::pengajuanPrasidang');
    $routes->get('pengajuanSidangSkripsi', 'TenagaKependidikan::pengajuanSidangSkripsi');

    // Rute Notifikasi (AJAX)
    $routes->get('fetchNotif', 'TenagaKependidikan::fetchNotif');
    $routes->get('markAsRead', 'TenagaKependidikan::markAsRead');

    // Rute Halaman Riwayat
    $routes->get('semua_notifikasi', 'TenagaKependidikan::semua_notifikasi');

    // Fitur Hardcover (GET)
    $routes->get('verifikasiHardcover', 'TenagaKependidikan::verifikasiHardcover');
    $routes->get('detailVerifikasiHardcover/(:num)', 'TenagaKependidikan::detailVerifikasiHardcover/$1');
    $routes->get('unggahHardcover', 'TenagaKependidikan::unggahHardcover');
});

/* * ====================================================================
 * SOLUSI: TARUH ROUTE POST DI LUAR GROUP (Global Route)
 * Ini mem-bypass filter route yang mungkin bermasalah/error
 * ====================================================================
 */

// Hardcover Actions
$routes->post('TenagaKependidikan/setujuiHardcover/(:num)', 'TenagaKependidikan::setujuiHardcover/$1');
$routes->post('TenagaKependidikan/tolakHardcover/(:num)', 'TenagaKependidikan::tolakHardcover/$1');
$routes->post('TenagaKependidikan/simpanHardcoverManual', 'TenagaKependidikan::simpanHardcoverManual');

// Cetak Berita Acara (YANG TADI ERROR)
$routes->post('TenagaKependidikan/cetakBeritaAcaraSempro', 'TenagaKependidikan::cetakBeritaAcaraSempro');
$routes->post('TenagaKependidikan/cetakBeritaAcaraSidang', 'TenagaKependidikan::cetakBeritaAcaraSidang');
$routes->post('TenagaKependidikan/updateAndCetakBASidang', 'TenagaKependidikan::updateAndCetakBASidang');