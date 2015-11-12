<?php
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Logger\Adapter\File as FileAdapter;
use adLDAP\adLDAP;
use adLDAP\adLDAPException;

use PhalconSeed\Plugins\Acl;
use PhalconSeed\Plugins\Security;
use PhalconSeed\Services\AuthService;
use PhalconSeed\Services\CommonService;
use PhalconSeed\Services\RoleService;
use PhalconSeed\Services\UserService;
use PhalconSeed\Services\LogService;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();
$di->set('view', function () use ($config) {
    return new View();
});

/**
 * Register the global configuration as config
 */
$di->set('config', $config);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {
    return new View();
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () use ($config) {
    if (isset($config->models->metadata)) {
        $metaDataConfig = $config->models->metadata;
        $metadataAdapter = 'Phalcon\Mvc\Model\Metadata\\' . $metaDataConfig->adapter;
        return new $metadataAdapter();
    }
    return new Phalcon\Mvc\Model\Metadata\Memory();
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});

/**
 * We register the events manager
 */
$di->set('dispatcher', function () use ($di) {

    // We listen for events in the dispatcher using the Security plugin
    $eventsManager = $di->getShared('eventsManager');
    $security = new Security($di);
    $eventsManager->attach('dispatch', $security);

    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    $dispatcher->setDefaultNamespace('\PhalconSeed\Controllers');

    return $dispatcher;
});

/**
 * Loading routes from the routes.php file
 */
$di->set('router', function () {
    return require __DIR__ . '/Routes.php';
});

/**
 * LDAP injection
 */
$di->set('ldap', function () {
    try {
        return new adLDAP();
    } catch (adLDAPException $e) {
        return null;
    }
});

/**
 * Access Control List
 */
$di->set('acl', function () {
    return new Acl();
});

/**
 * Models Manager for advanced queries
 */
$di->set('modelsManager', new ModelsManager());

/**
 * Logging crashs to file
 */
$di->set('crashLogger', new FileAdapter($config->application->crashLogFile));

/**
 * Logging actions to file
 */
$di->set('actionLogger', new FileAdapter($config->application->actionLogFile));

/**
 * Register Common Service
 */
$di->set('commonService', function () {
    return new CommonService();
});

/**
 * Register Authentication Service
 */
$di->set('authService', function () {
    return new AuthService();
});

/**
 * Register User Service
 */
$di->set('userService', function () {
    return new UserService();
});

/**
 * Register Group Service
 */
$di->set('roleService', function () {
    return new RoleService();
});

/**
 * Register Log Service
 */
$di->set('logService', function () {
    return new LogService();
});