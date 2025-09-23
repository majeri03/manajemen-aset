<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::processRegister');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');

$routes->get('/dashboard', 'Dashboard::index');



// Routes untuk halaman yang memerlukan login
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/dashboard', 'Dashboard::index');

    // Gunakan resource route untuk menangani semua URL aset
    // Ini sudah mencakup:
    // GET /aset -> AsetController::index() (halaman daftar)
    // POST /aset -> AsetController::create() (simpan data baru)
    // GET /aset/(:num) -> AsetController::show($id) (detail, API kita)
    // ...dan lainnya untuk edit/update/delete
    $routes->resource('aset', ['controller' => 'AsetController']);
    $routes->get('tracking', 'TrackingController::index');

    // Rute lama bisa dihapus jika sudah dicakup oleh resource
    // $routes->post('/dashboard/tambah-aset', 'AsetController::create'); // sudah dicakup
    // $routes->get('aset/detail/(:num)', 'AsetController::getDetail/$1'); // ganti ke 'show'
});


$routes->get('/dashboard', 'Dashboard::index');

    // TAMBAHKAN INI: Rute untuk proses simpan aset dari modal
    $routes->post('/dashboard/tambah-aset', 'AsetController::create');
    $routes->get('laporan', 'LaporanController::index');
    $routes->get('laporan/download/(:num)', 'LaporanController::download/$1');

    // Anda juga bisa menggunakan resource route untuk CRUD lengkap
    // $routes->resource('aset', ['controller' => 'AsetController']);

$routes->get('aset/detail/(:num)', 'AsetController::getDetail/$1');

// Tambahkan juga route untuk logout
$routes->get('/logout', 'AuthController::logout');

// TAMBAHKAN RUTE INI UNTUK PENCARIAN
$routes->get('aset/search', 'AsetController::search');

// Rute ini tidak perlu login
$routes->get('tracking/aset/(:num)', 'AsetController::publicDetail/$1');

// di dalam grup filter auth
$routes->resource('requests', ['controller' => 'RequestController']);

//  fungsi untuk mengekspor data yang sudah difilter
$routes->get('aset/laporan/export', 'AsetController::export');

$routes->get('dashboard/export/(:num)', 'AsetController::exportBulanan/$1');
