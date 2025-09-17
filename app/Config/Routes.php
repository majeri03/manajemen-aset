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