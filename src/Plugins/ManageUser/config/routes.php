<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

return static function (RouteBuilder $routes) {
    $routes->scope('/admin', ['plugin' => 'ManageUser'], function ($routes) {
        $routes->prefix('', function ($routes) {
//            $routes->connect('/users/process_user_permission_universities_products/*', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'processUserPermissionUniversitiesProducts']);
    });
});


    $routes->scope('/admin', ['plugin' => 'ManageUser'], function ($routes) {
        $routes->prefix('admin', function ($routes) {

        $routes->connect('/', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'login']);

        $routes->connect('/login', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'login']);

        $routes->connect('/dashboard/', ['plugin' => 'ManageUser', 'controller' => 'dashboard',  'action' => 'index', 'prefix' => 'admin']);

        $routes->connect('/logout', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'logout']);

        $routes->connect('/forgot-password', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'forgotPassword']);

        $routes->connect('/users', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'index']);

        $routes->connect('/users/edit/*', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'edit']);

        $routes->connect('/users/delete/*', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'delete']);

        $routes->connect('/users/change-state/*', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'changeState']);

        $routes->connect('/users/check-email/*', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'checkEmail']);

        $routes->fallbacks('DashedRoute');
    });
});
};
