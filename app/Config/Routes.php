<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->set404Override('App\Controllers\Api\Errors::NotFound');

/**
 * @var RouteCollection $routes
 */
$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {

    $routes->group('task', static function ($routes) {

        $routes->get('/', 'Task::index');
    });

    $routes->group('auth', static function ($routes) {

        $routes->post('/', 'Auth::login');
    });
});