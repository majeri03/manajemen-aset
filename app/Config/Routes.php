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

// Tambahkan juga route untuk logout
$routes->get('/logout', 'AuthController::logout');
