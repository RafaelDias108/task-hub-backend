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
// $routes->group('auth', ['namespace' => 'App\Controllers\Api'],  static function ($routes) {
//     $routes->post('/', 'Auth::login');
// });

/**
 * @var RouteCollection $routes
 */
$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {

    // Authentication route
    $routes->group('auth',  static function ($routes) {
        $routes->post('/', 'Auth::login');
    });

    // task route
    $routes->group('tasks', ['filter' => 'AuthFilter'], static function ($routes) {
        $routes->get('/', 'Task::index');
        $routes->get('(:uuid)', 'Task::index/$1');
        $routes->post('/', 'Task::NewTask');
        $routes->put('(:uuid)', 'Task::UpdateTask/$1');
        $routes->delete('(:uuid)', 'Task::DeleteTask/$1');
    });

    // project route
    $routes->group('projects', ['filter' => 'AuthFilter'], static function ($routes) {
        $routes->get('/', 'Project::index');
        $routes->get('(:uuid)', 'Project::index/$1');
        $routes->post('/', 'Project::NewProject');
        $routes->put('(:uuid)', 'Project::UpdateProject/$1');
        $routes->delete('(:uuid)', 'Project::DeleteProject/$1');
        $routes->post('linkCategory', 'Project::LinkCategoryToProject');
        $routes->post('unlinkCategory', 'Project::UnlinkCategoryToProject');
    });

    // create account route
    $routes->group('users', static function ($routes) {
        $routes->post('/', 'User::NewUser');
    });

    // accounts route
    $routes->group('users', ['filter' => 'AuthFilter'], static function ($routes) {
        $routes->get('(:uuid)', 'User::index/$1');
        $routes->put('(:uuid)', 'User::UpdateUser/$1');
        $routes->delete('(:uuid)', 'User::DeleteUser/$1');
    });

    // categories route
    $routes->group('categories', ['filter' => 'AuthFilter'], static function ($routes) {
        $routes->get('/', 'Category::index');
        $routes->get('(:uuid)', 'Category::index/$1');
        $routes->post('/', 'Category::NewCategory');
        $routes->put('(:uuid)', 'Category::UpdateCategory/$1');
        $routes->delete('(:uuid)', 'Category::DeleteCategory/$1');
    });
});