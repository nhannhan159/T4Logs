<?php
namespace PhalconSeed\Routes;

class LogRoute extends \Phalcon\Mvc\Router\Group {

    public function initialize() {

        // All the routes start with /api/log
        $this->setPrefix('/logs');

        // Add a route to the group
        $this->add('', array(
            'controller' => 'log-api',
            'action' => 'getAll'
        ))->via(array('OPTIONS', 'GET'));
    }

}