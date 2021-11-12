<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

return static function (RouteBuilder $routes) {
    $routes->plugin(
    'OAuthServer',
    ['path' => '/oauth'],
    function (RouteBuilder $routes) {
        //$routes->fallbacks(DashedRoute::class);
        $routes->setExtensions(['json']);
        $routes->connect(
            '/',
            [
                'controller' => 'OAuth',
                'action' => 'oauth'
            ]
        );
        $routes->connect(
            '/authorize',
            [
                'controller' => 'OAuth',
                'action' => 'authorize'
            ]
        );
        $routes->connect(
            '/access_token',
            [
                'controller' => 'OAuth',
                'action' => 'accessToken'
            ]
        );
        $routes->connect(
            '/web_portal_access_token',
            [
                'controller' => 'OAuth',
                'action' => 'webPortalAccessToken'
            ]
        );
    }
);
};
