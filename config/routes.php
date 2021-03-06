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
    $routes->connect('/news/view/*', ['controller' => 'News', 'action' => 'view']);

    $routes->prefix('admin', function ($routes) {

        $routes->connect('/', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'login']);

        $routes->connect('/login', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'login']);

        $routes->connect('/dashboard', ['plugin' => 'ManageUser', 'controller' => 'Users',  'action' => 'dashboard']);

//      Customer Sections
        $routes->connect('/customers/add', ['controller' => 'Customers',  'action' => 'add']);

        $routes->connect('/customers', ['controller' => 'Customers',  'action' => 'index']);

        $routes->connect('/customers/check-email', ['controller' => 'Customers',  'action' => 'checkEmail']);

        //News Sections
        $routes->connect('/news/add', ['controller' => 'News',  'action' => 'add']);

        $routes->connect('/news/edit/*', ['controller' => 'News',  'action' => 'edit']);

        $routes->connect('/news/delete/*', ['controller' => 'News',  'action' => 'delete']);

        //Orders Sections
        $routes->connect('/orders', ['controller' => 'Orders',  'action' => 'index']);

        $routes->connect('/orders/cancel-orders', ['controller' => 'Orders',  'action' => 'cancelOrderView']);

        $routes->connect('/orders/view/*', ['controller' => 'Orders',  'action' => 'view']);

        $routes->connect('/orders/cancel/*', ['controller' => 'Orders',  'action' => 'cancelOrder']);

        $routes->connect('/orders/complete/*', ['controller' => 'Orders',  'action' => 'completeOrder']);

        $routes->connect('/orders/order-edit/*', ['controller' => 'Orders',  'action' => 'orderEdit']);

        $routes->connect('/orders/order-product-edit/*', ['controller' => 'Orders',  'action' => 'orderProductEdit']);

//      Product Sections
        $routes->connect('/products/add', ['controller' => 'Products',  'action' => 'add']);

        $routes->connect('/products/edit/*', ['controller' => 'Products',  'action' => 'edit']);

        $routes->connect('/products/delete/*', ['controller' => 'Products',  'action' => 'delete']);

        $routes->connect('/products', ['controller' => 'Products',  'action' => 'index']);

        $routes->connect('/products/upload-image/*', ['controller' => 'Products',  'action' => 'uploadImage']);

        $routes->connect('/products/image-delete/*', ['controller' => 'Products',  'action' => 'imageDelete']);

//      Categories Sections

        $routes->connect('/categories/add', ['controller' => 'Categories',  'action' => 'add']);

        $routes->connect('/categories/edit/*', ['controller' => 'Categories',  'action' => 'edit']);

        $routes->connect('/categories', ['controller' => 'Categories',  'action' => 'index']);


        $routes->connect('/logout', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'logout']);

        $routes->connect('/forgot-password', ['plugin' => 'ManageUser', 'controller' => 'Users', 'action' => 'forgotPassword']);

        // User Sections

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

        //internal software apis
        $routes->connect('/users/api-user-login', ['controller' => 'LocalDevices', 'action' => 'login']);
        $routes->connect('/users/create-user', ['controller' => 'LocalDevices', 'action' => 'createUser']);
        $routes->connect('/users/refresh-token', ['controller' => 'LocalDevices', 'action' => 'getTokenByRefreshToken']);
        $routes->connect('/users/profile-image-change', ['controller' => 'LocalDevices', 'action' => 'profileImageChange']);
        $routes->connect('/users/update-user-info', ['controller' => 'LocalDevices', 'action' => 'updateProfileInfo']);
        $routes->connect('/users/user-info', ['controller' => 'LocalDevices', 'action' => 'getUserInfo']);
        $routes->connect('/users/change-password', ['controller' => 'LocalDevices', 'action' => 'changePassword']);


        $routes->connect('/address/save-address', ['controller' => 'LocalDevices', 'action' => 'saveAddress']);

        $routes->connect('/products/getAllProductsByCategory', ['controller' => 'LocalDevices', 'action' => 'getAllProductsByCategory']);
        $routes->connect('/products/getProduct', ['controller' => 'LocalDevices', 'action' => 'getProduct']);
        $routes->connect('/products/getAddressList', ['controller' => 'LocalDevices', 'action' => 'getAddress']);
        $routes->connect('/products/addToFavouriteProduct', ['controller' => 'LocalDevices', 'action' => 'addToFavouriteProduct']);
        $routes->connect('/products/getFavouriteProducts', ['controller' => 'LocalDevices', 'action' => 'getFavouriteProducts']);
        $routes->connect('/products/getRecentlyView', ['controller' => 'LocalDevices', 'action' => 'getRecentlyView']);
        $routes->connect('/products/filter-products', ['controller' => 'LocalDevices', 'action' => 'filterProducts']);


        $routes->connect('/checkout', ['controller' => 'Checkout', 'action' => 'index']);
        $routes->connect('/cart/add', ['controller' => 'Cart', 'action' => 'add']);
        $routes->connect('/cart/change', ['controller' => 'Cart', 'action' => 'change']);
        $routes->connect('/cart/remove', ['controller' => 'Cart', 'action' => 'remove']);

        $routes->connect('/payment', ['controller' => 'Payment', 'action' => 'payViaAjax']);
        $routes->connect('/payment/success', ['controller' => 'Payment', 'action' => 'success']);
        $routes->connect('/payment/fail', ['controller' => 'Payment', 'action' => 'fail']);
        $routes->connect('/payment/cancel', ['controller' => 'Payment', 'action' => 'cancel']);

        $routes->connect('/order/order-history', ['controller' => 'Order', 'action' => 'orderHistory']);
        $routes->connect('/order/get-order', ['controller' => 'Order', 'action' => 'getOrder']);
        $routes->connect('/order/cancel-order', ['controller' => 'Order', 'action' => 'orderCancel']);
        $routes->connect('/order/save-order-note', ['controller' => 'Order', 'action' => 'saveOrderNote']);


        $routes->connect('/news/news-list', ['controller' => 'News', 'action' => 'newsList']);

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

