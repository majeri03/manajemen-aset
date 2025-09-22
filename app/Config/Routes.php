<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// --- Rute Publik (Tidak Perlu Login) ---
$routes->get('/', 'Home::index');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::processRegister');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');
$routes->get('tracking/aset/(:num)', 'AsetController::publicDetail/$1');

// --- Rute yang Memerlukan Login (Semua Role) ---
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
    $routes->get('/logout', 'AuthController::logout');

    // Mengelola semua URL aset (index, show, create, update, delete)
    $routes->resource('aset', ['controller' => 'AsetController']);
    
    // Rute untuk pencarian dan ekspor laporan aset
    $routes->get('aset/search', 'AsetController::search');
    $routes->get('aset/laporan/export', 'AsetController::export');

    // Rute untuk Staff mengajukan permintaan perubahan
    $routes->get('requests/new/(:num)', 'RequestController::newRequest/$1');
    $routes->post('requests/store', 'RequestController::store');

    $routes->get('aset/history/(:num)', 'AsetController::getHistory/$1');
});

// --- Rute Khusus untuk Admin ---
$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    // Halaman untuk melihat dan mengelola daftar permintaan
    $routes->get('requests', 'RequestController::index');
    
    // Aksi untuk menyetujui atau menolak permintaan
    $routes->get('requests/approve/(:num)', 'RequestController::approve/$1');
    $routes->get('requests/reject/(:num)', 'RequestController::reject/$1');
});

