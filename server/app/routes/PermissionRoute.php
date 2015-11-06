<?php
namespace PhalconSeed\Routes;

class PermissionRoute extends \Phalcon\Mvc\Router\Group {

    public function initialize() {

        // All the routes start with /api/permissions
        $this->setPrefix('/api/permissions');

        $this->add('', array(
            'controller' => 'permission-api',
            'action' => 'getResources'
        ))->via(array('OPTIONS', 'GET'));

        $this->add('/{role_name}', array(
            'controller' => 'permission-api',
            'action' => 'getPermissions'
        ))->via(array('OPTIONS', 'GET'));

        $this->add('', array(
            'controller' => 'permission-api',
            'action' => 'assign'
        ))->via(array('OPTIONS', 'POST'));

    }
}