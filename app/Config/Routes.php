<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->set404Override('App\Controllers\Api\Errors::NotFound');
$routes->addPlaceholder('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

/**
 * Authentication api route
 */
$routes->group('auth', ['namespace' => 'App\Controllers\Api'],  static function ($routes) {
    $routes->post('/', 'Auth::login');
});

/**
 * @var RouteCollection $routes
 */
$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => 'AuthFilter'], static function ($routes) {

    $routes->group('task', static function ($routes) {
        $routes->get('/', 'Task::index');
    });

    $routes->group('projects', static function ($routes) {
        $routes->get('/', 'Project::index');
        $routes->get('/(:uuid)', 'Project::GetProjectByUID/$1');
    });
});