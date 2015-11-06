<?php
$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces(array(
    'PhalconSeed\Models' => $config->application->modelsDir,
    'PhalconSeed\Controllers' => $config->application->controllersDir,
    'PhalconSeed\Services' => $config->application->servicesDir,
    'PhalconSeed\Plugins' => $config->application->pluginsDir,
    'PhalconSeed\Routes' => $config->application->routesDir,
    'PhalconSeed\Exceptions' => $config->application->exceptionsDir,
    'PhalconSeed\AppConstants' => $config->application->constantsDir,
));

$loader->register();

// Use composer autoloader to load vendor classes
require_once __DIR__ . '/../../vendor/autoload.php';

// Use adLDAP for ldap service
require_once __DIR__ . '/../../app/library/adLDAP/adLDAP.php';
