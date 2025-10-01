<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Rute Publik (Tidak Perlu Login) ---
$routes->get('/', 'Home::index');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');

$routes->get('/logout', 'AuthController::logout');
// $routes->get('tracking/aset/(:num)', 'AsetController::publicDetail/$1');


// --- Rute yang Memerlukan Login ---
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/export/(:num)', 'AsetController::exportBulanan/$1');

    // Manajemen Aset (CRUD, Search, History, Export)
    $routes->get('aset/search', 'AsetController::search');
    $routes->get('aset/history/(:num)', 'AsetController::getHistory/$1');
    $routes->get('aset/laporan/export', 'AsetController::export');
    $routes->get('aset/stockopname_history/(:num)', 'AsetController::getStockOpnameHistory/$1');
    $routes->get('aset/barcodes', 'AsetController::barcodes');
    
    $routes->resource('aset', ['controller' => 'AsetController']);

    // Permintaan Perubahan Aset
    $routes->get('requests/new/(:num)', 'RequestController::newRequest/$1');
    $routes->get('requests/approve/(:num)', 'RequestController::approve/$1');
    $routes->get('requests/reject/(:num)', 'RequestController::reject/$1');  
    // $routes->post('requests/store', 'RequestController::store');
    $routes->resource('requests', ['controller' => 'RequestController']);
    
    // Pelacakan & Laporan
    $routes->get('stockopname', 'StockOpnameController::index');
    $routes->get('laporan', 'LaporanController::index');
    $routes->get('laporan/download/(:num)', 'LaporanController::download/$1');


    // --- [MULAI] Rute Baru untuk Data Master ---
    // Baris ini akan menampilkan halaman utama Data Master
    $routes->get('master-data', 'MasterDataController::index');
    
    // Rute untuk mengelola Kategori
    $routes->post('master-data/kategori/create', 'MasterDataController::createKategori');
    $routes->post('master-data/kategori/update/(:num)', 'MasterDataController::updateKategori/$1');
    $routes->get('master-data/kategori/delete/(:num)', 'MasterDataController::deleteKategori/$1');
    
    // Rute untuk mengelola Sub-Kategori
    $routes->post('master-data/subkategori/create', 'MasterDataController::createSubKategori');
    $routes->post('master-data/subkategori/update/(:num)', 'MasterDataController::updateSubKategori/$1');
    $routes->get('master-data/subkategori/delete/(:num)', 'MasterDataController::deleteSubKategori/$1');
    
    // Rute untuk mengelola Lokasi
    $routes->post('master-data/lokasi/create', 'MasterDataController::createLokasi');
    $routes->post('master-data/lokasi/update/(:num)', 'MasterDataController::updateLokasi/$1');
    $routes->get('master-data/lokasi/delete/(:num)', 'MasterDataController::deleteLokasi/$1');

    // Rute untuk mengelola Merk
    $routes->post('master-data/merk/create', 'MasterDataController::createMerk');
    $routes->post('master-data/merk/update/(:num)', 'MasterDataController::updateMerk/$1');
    $routes->get('master-data/merk/delete/(:num)', 'MasterDataController::deleteMerk/$1');

    // Rute untuk mengelola Tipe
    $routes->post('master-data/tipe/create', 'MasterDataController::createTipe');
    $routes->post('master-data/tipe/update/(:num)', 'MasterDataController::updateTipe/$1');
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

    //Rute MANAJEMEN AKUN
    $routes->get('user', 'UserController::index');
    $routes->get('user/create', 'UserController::create');
    $routes->post('user/store', 'UserController::store');
    $routes->get('user/edit/(:num)', 'UserController::edit/$1');
    $routes->post('user/update/(:num)', 'UserController::update/$1');
    $routes->get('user/delete/(:num)', 'UserController::delete/$1');
    $routes->delete('user/delete/(:num)', 'UserController::delete/$1'); // Menggunakan metode DELETE
    $routes->post('user/activate/(:num)', 'UserController::activate/$1');
    $routes->post('user/reset-password/(:num)', 'UserController::resetPassword/$1'); // Rute baru

    //rute stockopname
    $routes->get('stockopname/aset/(:num)', 'StockOpnameController::view/$1');
    $routes->get('stockopname/export', 'StockOpnameController::export');
    $routes->post('stockopname/process/(:num)', 'StockOpnameController::process/$1');
    
    $routes->post('user/toggle-so-mode', 'UserController::toggleSoMode');
    $routes->post('user/toggle-so-permission/(:num)', 'UserController::toggleUserSoPermission/$1');
});