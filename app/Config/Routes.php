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


    // --- [MULAI] Rute Baru untuk Data Master ---
    // Baris ini akan menampilkan halaman utama Data Master
    $routes->get('master-data', 'MasterDataController::index');
    
    // Rute untuk mengelola Kategori
    $routes->post('master-data/kategori/create', 'MasterDataController::createKategori');
    $routes->get('master-data/kategori/delete/(:num)', 'MasterDataController::deleteKategori/$1');
    
    // Rute untuk mengelola Sub-Kategori
    $routes->post('master-data/subkategori/create', 'MasterDataController::createSubKategori');
    $routes->get('master-data/subkategori/delete/(:num)', 'MasterDataController::deleteSubKategori/$1');
    
    // Rute untuk mengelola Lokasi
    $routes->post('master-data/lokasi/create', 'MasterDataController::createLokasi');
    $routes->get('master-data/lokasi/delete/(:num)', 'MasterDataController::deleteLokasi/$1');

    // Rute untuk mengelola Merk
    $routes->post('master-data/merk/create', 'MasterDataController::createMerk');
    $routes->get('master-data/merk/delete/(:num)', 'MasterDataController::deleteMerk/$1');

    // Rute untuk mengelola Tipe
    $routes->post('master-data/tipe/create', 'MasterDataController::createTipe');
    $routes->get('master-data/tipe/delete/(:num)', 'MasterDataController::deleteTipe/$1');

    // Rute API untuk dropdown dinamis
    $routes->get('api/tipe/(:num)', 'AsetController::getTipesByMerk/$1');

    //Rute Untuk Fitur Import
    $routes->get('import', 'ImportController::index');
    $routes->post('import/upload', 'ImportController::upload');
    $routes->post('import/save', 'ImportController::save');
    $routes->get('import/cancel', 'ImportController::cancel');
    $routes->post('import/add-master', 'ImportController::addMasterData'); // Untuk AJAX
    $routes->get('import/print-labels', 'ImportController::printLabels');
    $routes->get('import/template', 'ImportController::downloadTemplate');

    // [BARU] Tambahkan rute ini untuk auto-save
    $routes->post('import/update-session', 'ImportController::updateSessionData');
    $routes->post('import/delete-master', 'ImportController::deleteMasterData');

});