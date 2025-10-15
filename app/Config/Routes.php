<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Rute Publik (Tidak Perlu Login) ---
$routes->get('/', 'AuthController::login'); // Diarahkan ke login jika belum masuk
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/aset/info/(:num)', 'AsetController::infoPublik/$1');

// RUTE BARU UNTUK LUPA PASSWORD
$routes->get('/forgot-password', 'AuthController::forgotPassword');
$routes->post('/forgot-password', 'AuthController::processForgotPassword');
$routes->get('/reset-password/(:hash)', 'AuthController::resetPassword/$1');
$routes->post('/reset-password/(:hash)', 'AuthController::processResetPassword/$1');

// --- Rute yang Memerlukan Login ---
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    // ===================================================================
    // RUTE YANG BISA DIAKSES SEMUA PERAN (ADMIN & STAFF)
    // ===================================================================
    $routes->get('dashboard', 'Dashboard::index');
    
    // Aset (Hanya melihat daftar dan detail)
    $routes->get('aset', 'AsetController::index'); 
    $routes->get('aset/search', 'AsetController::search');
    $routes->get('aset/history/(:num)', 'AsetController::getHistory/$1');
    $routes->get('aset/stockopname_history/(:num)', 'AsetController::getStockopnameHistory/$1');
    $routes->get('aset/(:num)', 'AsetController::show/$1'); // Detail aset

    // Stock Opname (Fitur utama Staff)
    // $routes->get('stockopname/aset/(:num)', 'StockOpnameController::view/$1');
    // $routes->post('stockopname/process/(:num)', 'StockOpnameController::process/$1');

    // Permintaan (Hanya untuk membuat pengajuan baru)
    $routes->get('requests/new/(:num)', 'RequestController::newRequest/$1');
    $routes->post('requests', 'RequestController::create'); // Rute untuk menyimpan pengajuan

    // API (Umumnya dibutuhkan di banyak tempat)
    $routes->get('api/tipe/(:num)', 'AsetController::getTipesByMerk/$1');

    // Scan Cepat
    $routes->get('scan-cepat', 'StockOpnameController::scanCepat');
    $routes->post('stockopname/process-scan', 'StockOpnameController::processScan');
    $routes->get('api/last-verification/(:num)', 'StockOpnameController::getLastVerificationInfo/$1');
    $routes->get('api/aset-by-location/(:num)', 'StockOpnameController::getAsetByLocation/$1');
    $routes->post('quick-scan-store', 'StockOpnameController::quickScanStore');
    $routes->match(['get', 'post'], 'stockopname/session-report', 'StockOpnameController::sessionReport');
   
    //profile
    $routes->get('profile', 'UserController::profile');
    $routes->post('user/profile', 'UserController::updateProfileInfo'); // Mengubah info dasar
    $routes->post('user/update-password', 'UserController::updatePassword'); // Khusus untuk password

    $routes->get('files/bukti/(:segment)', 'AsetController::serveDocument/$1');
    // ===================================================================
    // RUTE YANG HANYA BISA DIAKSES OLEH ADMIN
    // ===================================================================
    $routes->group('', ['filter' => 'role:admin'], static function ($routes) {
        
        // Dashboard (Fitur khusus admin)
        $routes->get('dashboard/export/(:num)', 'AsetController::exportBulanan/$1');

        // Manajemen Aset (Akses Penuh: Tambah, Edit, Hapus)
        $routes->get('aset/new', 'AsetController::new');
        $routes->post('aset', 'AsetController::create');
        $routes->get('aset/(:num)/edit', 'AsetController::edit/$1');
        $routes->put('aset/(:num)', 'AsetController::update/$1');
        $routes->post('aset/(:num)', 'AsetController::update/$1'); // Fallback untuk form tanpa PUT
        $routes->delete('aset/(:num)', 'AsetController::delete/$1');
        //$routes->post('aset/delete-document/(:num)', 'AsetController::deleteDocument/$1');
        $routes->get('aset/barcodes', 'AsetController::barcodes');
        $routes->get('aset/laporan/export', 'AsetController::export');

        $routes->post('aset/add-dokumentasi/(:num)', 'AsetController::add_dokumentasi/$1');
        $routes->post('aset/add-berkas/(:num)', 'AsetController::add_berkas/$1');
        $routes->post('aset/delete-document/(:num)/(:alpha)', 'AsetController::delete_document/$1/$2');
        $routes->get('files/bukti/(:segment)', 'AsetController::serveDocument/$1');

        // Permintaan Perubahan Aset (Melihat daftar & persetujuan)
        $routes->get('requests', 'RequestController::index');
        $routes->get('requests/approve/(:num)', 'RequestController::approve/$1');
        $routes->get('requests/reject/(:num)', 'RequestController::reject/$1');
        
        // Pelacakan & Laporan Stockopname
        $routes->get('stockopname', 'StockOpnameController::index');
        $routes->get('stockopname/export', 'StockOpnameController::export');
        $routes->get('laporan', 'LaporanController::index');
        $routes->get('laporan/download/(:num)', 'LaporanController::download/$1');
        $routes->get('laporan/stockopname', 'LaporanController::stockOpnameReport');
        $routes->get('laporan/stockopname/export/(:num)', 'LaporanController::exportStockOpnameReport/$1');

        
        $routes->post('stockopname/process-scan-report', 'StockOpnameController::processScanReport'); // Kita akan buat fungsi ini nanti
        $routes->post('stockopname/update-session-report', 'StockOpnameController::updateSessionReportData');

        // Data Master
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

        // TAMBAHKAN RUTE KARYAWAN DI SINI
        $routes->post('master-data/karyawan/create', 'MasterDataController::createKaryawan');
        $routes->get('master-data/karyawan/delete/(:num)', 'MasterDataController::deleteKaryawan/$1');

        // Fitur Import
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

        $routes->post('/master-data/add-sub-kategori', 'MasterDataController::addSubKategori', ['filter' => 'auth']);
        $routes->post('/master-data/add-tipe', 'MasterDataController::addTipe', ['filter' => 'auth']);
        $routes->post('/import/validate-row', 'ImportController::validateRow', ['filter' => 'auth']);

        
        //download serah terima pdf
        $routes->get('aset/generate-pdf/(:num)/(:num)', 'AsetController::generateSerahTerimaPdf/$1/$2');

        // Manajemen Akun
        $routes->get('user', 'UserController::index');
        $routes->get('user/create', 'UserController::create');
        $routes->post('user/store', 'UserController::store');
        $routes->get('user/edit/(:num)', 'UserController::edit/$1');
        $routes->post('user/update/(:num)', 'UserController::update/$1');
        $routes->get('user/delete/(:num)', 'UserController::delete/$1');
        $routes->delete('user/delete/(:num)', 'UserController::delete/$1');
        $routes->post('user/activate/(:num)', 'UserController::activate/$1');
        $routes->post('user/reset-password/(:num)', 'UserController::resetPassword/$1');
        $routes->post('user/toggle-so-mode', 'UserController::toggleSoMode');
        $routes->post('user/toggle-so-permission/(:num)', 'UserController::toggleUserSoPermission/$1');


        //siklus stock opname baru
        $routes->post('stockopname/start-cycle', 'StockOpnameController::startCycle');
        $routes->get('laporan/lokasi/(:num)', 'LaporanController::laporanLokasiDetail/$1');
    });
});