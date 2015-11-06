<?php
namespace PhalconSeed\Routes;

class RoleRoute extends \Phalcon\Mvc\Router\Group {

    public function initialize() {

        // All the routes start with /api/role
        $this->setPrefix('/api/roles');

        $this->add('', array(
            'controller' => 'role-api',
            'action' => 'getAll'
        ))->via(array('OPTIONS', 'GET'));

        $this->add('/{role_name}', array(
            'controller' => 'role-api',
            'action' => 'create'
        ))->via(array('OPTIONS', 'POST'));

        $this->add('/{role_name}', array(
            'controller' => 'role-api',
            'action' => 'getUsers'
        ))->via(array('OPTIONS', 'GET'));

        $this->add('/{role_name}', array(
            'controller' => 'role-api',
            'action' => 'delete'
        ))->via(array('OPTIONS', 'DELETE'));

        $this->add('/{role_name}/{username}', array(
            'controller' => 'role-api',
            'action' => 'assign'
        ))->via(array('OPTIONS', 'POST'));


    }
}