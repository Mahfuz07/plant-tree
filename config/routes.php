<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Authentication\AuthenticationService;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Http\Middleware\EncryptedCookieMiddleware;
use Authentication\Middleware\AuthenticationMiddleware;

$routes->setRouteClass(DashedRoute::class);

$routes->registerMiddleware('cookie', new EncryptedCookieMiddleware(['cookie_names'], 'ISdysydisayd'));
$routes->registerMiddleware('auth', new AuthenticationMiddleware(new AuthenticationService()));
$routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
    'httponly' => true,
]));
$routes->middlewareGroup('web', ['cookie','auth', 'csrf']);

return static function (RouteBuilder $routes) {
    $routes->setRouteClass(DashedRoute::class);


//    $routes->scope('/', function (RouteBuilder $builder) {
//        $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
//        $builder->connect('/pages/*', 'Pages::display');
//        $builder->fallbacks();
//    });

    $routes->connect('/', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'login']);

    $routes->prefix('admin', function ($routes) {

            $routes->connect('/', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'login']);

            $routes->connect('/login', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'login']);

            $routes->connect('/dashboard', ['plugin' => 'ManageUser', 'controller' => 'Users',  'action' => 'dashboard']);

            $routes->connect('/customers/add', ['controller' => 'Customers',  'action' => 'add']);

            $routes->connect('/products/add', ['controller' => 'Products',  'action' => 'add']);

            $routes->connect('/products/edit/*', ['controller' => 'Products',  'action' => 'edit']);

            $routes->connect('/products', ['controller' => 'Products',  'action' => 'index']);

            $routes->connect('/categories/add', ['controller' => 'Categories',  'action' => 'add']);

            $routes->connect('/customers/check-email', ['controller' => 'Customers',  'action' => 'checkEmail']);

            $routes->connect('/logout', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'logout']);

            $routes->connect('/forgot-password', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'forgotPassword']);

            $routes->connect('/users', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'index']);

            $routes->connect('/users/edit/*', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'edit']);

            $routes->connect('/users/delete/*', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'delete']);

            $routes->connect('/users/change-state/*', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'changeState']);

            $routes->connect('/users/check-email/*', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'checkEmail']);

            $routes->fallbacks('DashedRoute');
        });


    $routes->prefix('api', function ($routes) {
        $routes->setExtensions(['json', 'xml']);
        $routes->fallbacks('InflectedRoute');

        //device apis
//    $routes->connect('/users/users/get_token_by_refresh_token', ['controller' => 'LocalDevices', 'action' => 'getToken']);
//    $routes->connect('/users/users/api_user_login/staff', ['controller' => 'LocalDevices', 'action' => 'login']);


        //internal software apis
        $routes->connect('/users/api-user-login', ['controller' => 'LocalDevices', 'action' => 'login']);
        $routes->connect('/users/create-user', ['controller' => 'LocalDevices', 'action' => 'createUser']);
        $routes->connect('/users/refresh-token', ['controller' => 'LocalDevices', 'action' => 'getTokenByRefreshToken']);
//    $routes->connect('/users/users/api_user_role_login', ['controller' => 'LocalDevices', 'action' => 'loginRole']);
//    $routes->connect('/users/users/add_user', ['controller' => 'LocalDevices', 'action' => 'addUser']);
//    $routes->connect('/users/users/api_forgot_password', ['controller' => 'LocalDevices', 'action' => 'apiForgotPassword']);
//    $routes->connect('/users/users/api_reset', ['controller' => 'LocalDevices', 'action' => 'apiReset']);
//    $routes->connect('/users/users/api_check_email', ['controller' => 'LocalDevices', 'action' => 'apiCheckEmail']);
//    $routes->connect('/users/users/api-reset-password', ['controller' => 'LocalDevices', 'action' => 'apiResetPassword']);
//    $routes->connect('/users/users/api-change-password', ['controller' => 'LocalDevices', 'action' => 'apiChangePassword']);
//    $routes->connect('/users/users/api-user-add', ['controller' => 'LocalDevices', 'action' => 'apiUserAdd']);

    });

};

