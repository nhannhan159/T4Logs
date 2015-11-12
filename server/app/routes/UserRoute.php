<?php
namespace PhalconSeed\Routes;

class UserRoute extends \Phalcon\Mvc\Router\Group {

    public function initialize() {

        // All the routes start with /api/user
        $this->setPrefix('/users');

        // Add a route to the group
        $this->add('', array(
            'controller' => 'user-api',
            'action' => 'getAll'
        ))->via(array('OPTIONS', 'GET'));

        $this->add('', array(
            'controller' => 'user-api',
            'action' => 'create'
        ))->via(array('OPTIONS', 'POST'));

        $this->add('/{id}', array(
            'controller' => 'user-api',
            'action' => 'getById'
        ))->via(array('OPTIONS', 'GET'));

        $this->add('/{id}', array(
            'controller' => 'user-api',
            'action' => 'delete'
        ))->via(array('OPTIONS', 'DELETE'));

        $this->add('/{id}/roles', array(
            'controller' => 'user-api',
            'action' => 'getRoles'
        ))->via(array('OPTIONS', 'GET'));

        $this->add('/{id}/password', array(
            'controller' => 'user-api',
            'action' => 'changePassword'
        ))->via(array('OPTIONS', 'PUT'));

    }

}