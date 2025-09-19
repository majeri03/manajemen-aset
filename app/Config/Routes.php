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
});


$routes->get('/dashboard', 'Dashboard::index');

    // TAMBAHKAN INI: Rute untuk proses simpan aset dari modal
    $routes->post('/dashboard/tambah-aset', 'AsetController::create');

    // Anda juga bisa menggunakan resource route untuk CRUD lengkap
    // $routes->resource('aset', ['controller' => 'AsetController']);

$routes->get('aset/detail/(:num)', 'AsetController::getDetail/$1');

// Tambahkan juga route untuk logout
$routes->get('/logout', 'AuthController::logout');

// TAMBAHKAN RUTE INI UNTUK PENCARIAN
$routes->get('aset/search', 'AsetController::search');
