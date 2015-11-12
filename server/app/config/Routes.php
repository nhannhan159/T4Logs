<?php
use PhalconSeed\Routes\AuthRoute;
use PhalconSeed\Routes\UserRoute;
use PhalconSeed\Routes\RoleRoute;
use PhalconSeed\Routes\PermissionRoute;
use PhalconSeed\Routes\LogRoute;

/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router(false);

// Add the group of auth route to the router
$router->mount(new AuthRoute());

// Add the group of user route to the router
$router->mount(new UserRoute());

// Add the group of role route to the router
$router->mount(new RoleRoute());

// Add the group of permission route to the router
$router->mount(new PermissionRoute());

// Add the group of log route to the router
$router->mount(new LogRoute());

return $router;
