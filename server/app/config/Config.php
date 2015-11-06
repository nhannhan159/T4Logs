<?php
return new \Phalcon\Config(array(
    'database' => array(
        'host' => '127.0.0.1',
        'port' => 3306,
        'username' => 'phalcon',
        'password' => 'phalcon',
        'dbname' => 'phalcon',
        'charset' => 'utf8',
    ),
    'application' => array(
        'constantsDir' => APP_DIR . '/constants/',
        'controllersDir' => APP_DIR . '/controllers/',
        'exceptionsDir' => APP_DIR . '/exceptions/',
        'libraryDir' => APP_DIR . '/library/',
        'modelsDir' => APP_DIR . '/models/',
        'pluginsDir' => APP_DIR . '/plugins/',
        'routesDir' => APP_DIR . '/routes/',
        'servicesDir' => APP_DIR . '/services/',
        'baseUri' => '/',
        'publicUrl' => 'http://localhost:1234/phalcon/backend',
        'crashLogFile' => 'crash.log',
        'actionLogFile' => 'action.log',
        'ldapDomain' => 'ldap.forumsys.com',
        'ldapBaseDn' => 'dc=example,dc=com'
    ),
    'metadata' => array(
        'adapter' => 'Apc',
        'suffix' => 'phalcon',
        'lifetime' => 0,
        'lifetime_old' => 86400
    )
));