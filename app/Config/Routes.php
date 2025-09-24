<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Rute Publik (Tidak Perlu Login) ---
$routes->get('/', 'Home::index');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::processRegister');
$routes->get('/logout', 'AuthController::logout');
$routes->get('tracking/aset/(:num)', 'AsetController::publicDetail/$1');


// --- Rute yang Memerlukan Login ---
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/export/(:num)', 'AsetController::exportBulanan/$1');

    // Manajemen Aset (CRUD, Search, History, Export)
    $routes->get('aset/search', 'AsetController::search');
    $routes->get('aset/history/(:num)', 'AsetController::getHistory/$1');
    $routes->get('aset/laporan/export', 'AsetController::export');
    
    $routes->resource('aset', ['controller' => 'AsetController']);
    // Permintaan Perubahan Aset
    $routes->get('requests/new/(:num)', 'RequestController::newRequest/$1');
    $routes->get('requests/approve/(:num)', 'RequestController::approve/$1');
    $routes->get('requests/reject/(:num)', 'RequestController::reject/$1');  
    // $routes->post('requests/store', 'RequestController::store');
    $routes->resource('requests', ['controller' => 'RequestController']);
    
    // Pelacakan & Laporan
    $routes->get('tracking', 'TrackingController::index');
    $routes->get('laporan', 'LaporanController::index');
    $routes->get('laporan/download/(:num)', 'LaporanController::download/$1');

    // Rute untuk Manajemen Kategori
    $routes->get('category', 'CategoryController::index');
    $routes->post('category/createKategori', 'CategoryController::createKategori');
    $routes->post('category/updateKategori/(:num)', 'CategoryController::updateKategori/$1');
    $routes->get('category/deleteKategori/(:num)', 'CategoryController::deleteKategori/$1');

    $routes->post('category/createSubKategori', 'CategoryController::createSubKategori');
    $routes->post('category/updateSubKategori/(:num)', 'CategoryController::updateSubKategori/$1');
    $routes->get('category/deleteSubKategori/(:num)', 'CategoryController::deleteSubKategori/$1');

});