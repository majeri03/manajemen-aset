<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Rute Publik (Tidak Perlu Login) ---
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/aset/info/(:num)', 'AsetController::infoPublik/$1');

// --- Rute Lupa Password ---
$routes->get('/forgot-password', 'AuthController::forgotPassword');
$routes->post('/forgot-password', 'AuthController::processForgotPassword');
$routes->get('/reset-password/(:hash)', 'AuthController::resetPassword/$1');
$routes->post('/reset-password/(:hash)', 'AuthController::processResetPassword/$1');

// --- Grup utama untuk semua pengguna yang sudah login ---
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    // ===================================================================
    // RUTE UNTUK SEMUA ROLE (SUPER_ADMIN, ADMIN, STAFF)
    // ===================================================================
    $routes->get('dashboard', 'Dashboard::index');
    
    // Rute Aset dasar (melihat daftar & detail)
    $routes->get('aset', 'AsetController::index'); 
    $routes->get('aset/search', 'AsetController::search');
    $routes->get('aset/history/(:num)', 'AsetController::getHistory/$1');
    $routes->get('aset/stockopname_history/(:num)', 'AsetController::getStockopnameHistory/$1');
    $routes->get('aset/(:num)', 'AsetController::show/$1');

    // Rute untuk membuat pengajuan perubahan data
    $routes->get('requests/new/(:num)', 'RequestController::newRequest/$1');
    $routes->post('requests', 'RequestController::create');

    // Rute untuk Scan Cepat (Stock Opname)
    $routes->get('scan-cepat', 'StockOpnameController::scanCepat');
    $routes->post('stockopname/process-scan', 'StockOpnameController::processScan');
    $routes->match(['get', 'post'], 'stockopname/session-report', 'StockOpnameController::sessionReport');
    $routes->post('stockopname/process-scan-report', 'StockOpnameController::processScanReport');
    $routes->post('stockopname/update-session-report', 'StockOpnameController::updateSessionReportData');
   
    // Rute Profil Pengguna
    $routes->get('profile', 'UserController::profile');
    $routes->post('user/profile', 'UserController::updateProfileInfo');
    $routes->post('user/update-password', 'UserController::updatePassword');

    // Rute API & File umum
    $routes->get('api/tipe/(:num)', 'AsetController::getTipesByMerk/$1');
    $routes->get('api/last-verification/(:num)', 'StockOpnameController::getLastVerificationInfo/$1');
    $routes->get('api/aset-by-location/(:num)', 'StockOpnameController::getAsetByLocation/$1');
    $routes->get('files/bukti/(:segment)', 'AsetController::serveDocument/$1');

    // ===================================================================
    // RUTE HANYA UNTUK ADMIN & SUPER_ADMIN
    // ===================================================================
    $routes->group('', ['filter' => 'role:admin,super_admin'], static function ($routes) {
        
        // Fitur ekspor dari dashboard
        $routes->get('dashboard/export/(:num)', 'AsetController::exportBulanan/$1');

        // Manajemen Aset (Akses Penuh: Tambah, Edit, Hapus)
        $routes->post('aset', 'AsetController::create'); // Rute untuk 'create' dari modal
        $routes->get('aset/(:num)/edit', 'AsetController::edit/$1');
        $routes->put('aset/(:num)', 'AsetController::update/$1');
        $routes->post('aset/(:num)', 'AsetController::update/$1'); // Fallback
        $routes->delete('aset/(:num)', 'AsetController::delete/$1');
        $routes->get('aset/barcodes', 'AsetController::barcodes');
        $routes->get('aset/laporan/export', 'AsetController::export');
        
        // Manajemen Dokumen & Berkas Aset
        $routes->post('aset/add-dokumentasi/(:num)', 'AsetController::add_dokumentasi/$1');
        $routes->post('aset/add-berkas/(:num)', 'AsetController::add_berkas/$1');
        $routes->post('aset/delete-document/(:num)/(:alpha)', 'AsetController::delete_document/$1/$2');

        // Persetujuan Perubahan Aset
        $routes->get('requests', 'RequestController::index');
        $routes->get('requests/approve/(:num)', 'RequestController::approve/$1');
        $routes->get('requests/reject/(:num)', 'RequestController::reject/$1');
        
        // Manajemen Stock Opname & Laporan
        $routes->get('stockopname', 'StockOpnameController::index');
        $routes->post('stockopname/start-cycle', 'StockOpnameController::startCycle');
        $routes->get('laporan', 'LaporanController::index');
        $routes->get('laporan/stockopname', 'LaporanController::stockOpnameReport');
        $routes->get('laporan/stockopname/export/(:num)', 'LaporanController::exportStockOpnameReport/$1');
        $routes->get('laporan/lokasi/(:num)', 'LaporanController::laporanLokasiDetail/$1');

        // Generate PDF
        $routes->get('aset/generateSerahTerimaPdf/(:num)/(:num)', 'AsetController::generateSerahTerimaPdf/$1/$2');
        $routes->get('aset/generatePerbaikanPdf/(:num)', 'AsetController::generatePerbaikanPdf/$1');

        // Fitur Import Data
        $routes->get('import', 'ImportController::index');
        $routes->post('import/upload', 'ImportController::upload');
        $routes->post('import/save', 'ImportController::save');
        $routes->get('import/cancel', 'ImportController::cancel');
        $routes->post('import/add-master', 'ImportController::addMasterData');
        $routes->get('import/print-labels', 'ImportController::printLabels');
        $routes->get('import/template', 'ImportController::downloadTemplate');
        $routes->post('import/update-session', 'ImportController::updateSessionData');
        $routes->post('import/delete-master', 'ImportController::deleteMasterData');
        $routes->post('import/validate-row', 'ImportController::validate_row');
    });

    // ===================================================================
    // RUTE HANYA UNTUK SUPER_ADMIN
    // ===================================================================
    $routes->group('', ['filter' => 'role:super_admin'], static function ($routes) {
        
        // Manajemen Pengguna
        $routes->get('user', 'UserController::index');
        $routes->get('user/create', 'UserController::create');
        $routes->post('user/store', 'UserController::store');
        $routes->get('user/edit/(:num)', 'UserController::edit/$1');
        $routes->post('user/update/(:num)', 'UserController::update/$1');
        $routes->delete('user/delete/(:num)', 'UserController::delete/$1'); // Diganti ke delete
        $routes->post('user/activate/(:num)', 'UserController::activate/$1');
        $routes->post('user/reset-password/(:num)', 'UserController::resetPassword/$1');
        $routes->post('user/toggle-so-mode', 'UserController::toggleSoMode');
        $routes->post('user/toggle-so-permission/(:num)', 'UserController::toggleUserSoPermission/$1');

        // Manajemen Data Master
        $routes->get('master-data', 'MasterDataController::index');
        $routes->post('master-data/kategori/create', 'MasterDataController::createKategori');
        $routes->post('master-data/kategori/update/(:num)', 'MasterDataController::updateKategori/$1');
        $routes->get('master-data/kategori/delete/(:num)', 'MasterDataController::deleteKategori/$1');
        $routes->post('master-data/subkategori/create', 'MasterDataController::createSubKategori');
        $routes->post('master-data/subkategori/update/(:num)', 'MasterDataController::updateSubKategori/$1');
        $routes->get('master-data/subkategori/delete/(:num)', 'MasterDataController::deleteSubKategori/$1');
        $routes->post('master-data/lokasi/create', 'MasterDataController::createLokasi');
        $routes->post('master-data/lokasi/update/(:num)', 'MasterDataController::updateLokasi/$1');
        $routes->get('master-data/lokasi/delete/(:num)', 'MasterDataController::deleteLokasi/$1');
        $routes->post('master-data/merk/create', 'MasterDataController::createMerk');
        $routes->post('master-data/merk/update/(:num)', 'MasterDataController::updateMerk/$1');
        $routes->get('master-data/merk/delete/(:num)', 'MasterDataController::deleteMerk/$1');
        $routes->post('master-data/tipe/create', 'MasterDataController::createTipe');
        $routes->post('master-data/tipe/update/(:num)', 'MasterDataController::updateTipe/$1');
        $routes->get('master-data/tipe/delete/(:num)', 'MasterDataController::deleteTipe/$1');
        $routes->post('master-data/karyawan/create', 'MasterDataController::createKaryawan');
        $routes->get('master-data/karyawan/delete/(:num)', 'MasterDataController::deleteKaryawan/$1');
        $routes->post('master-data/add-sub-kategori', 'MasterDataController::addSubKategori');
        $routes->post('master-data/add-tipe', 'MasterDataController::addTipe');

        
    });
});