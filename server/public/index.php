<?php

header("Access-Control-Allow-Origin", "*");
error_reporting(E_ALL);

try {

    date_default_timezone_set("UTC");
    ini_set('date.timezone', 'UTC');

    define('BASE_DIR', dirname(__DIR__));
    define('APP_DIR', BASE_DIR . '/app');
    define('PUBLIC_DIR', BASE_DIR . '/public');

    /**
     * Read the configuration
     */
    $config = include APP_DIR . '/config/Config.php';

    /**
     * Read auto-loader
     */
    include APP_DIR . '/config/Loader.php';

    /**
     * Read services
     */
    include APP_DIR . '/config/Services.php';

    /**
     * Handle the request
     */
    $application = new Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

} catch (Phalcon\Exception $ex) {
    echo $ex->getMessage();
} catch (PDOException $ex) {
    echo $ex->getMessage();
}