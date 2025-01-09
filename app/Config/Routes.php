<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('/logout', 'AuthController::logout');
$routes->post('authverify', 'AuthController::login');

// gbn routes
$routes->group('/gbn', ['filter' => 'gbn'], function ($routes) {
    $routes->get('', 'MaterialController::index');
    $routes->get('pph', 'PphController::index');
    $routes->get('tampilPerStyle', 'PphController::tampilPerStyle');
    $routes->post('tampilPerStyle', 'PphController::tampilPerStyle');
    $routes->get('tampilPerDays', 'PphController::tampilPerDays');
    $routes->post('tampilPerDays', 'PphController::tampilPerDays');
    $routes->get('tampilPerModel', 'PphController::tampilPerModel');
    $routes->post('tampilPerModel', 'PphController::tampilPerModel');

});

// celup routes
$routes->group('/celup', ['filter' => 'celup'], function ($routes) {
    $routes->get('', 'inicontroller::index');
});



// covering routes
$routes->group('/covering', ['filter' => 'covering'], function ($routes) {
    $routes->get('', 'inicontroller::index');
});


// monitoring routes
$routes->group('/monitoring', ['filter' => 'monitoring'], function ($routes) {
    $routes->get('', 'inicontroller::index');
});
