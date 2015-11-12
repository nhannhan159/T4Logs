<?php
namespace PhalconSeed\Routes;

class AuthRoute extends \Phalcon\Mvc\Router\Group {

    public function initialize() {

        // All the routes start with /api/auth
        $this->setPrefix('/auth');

        $this->add('/login/{method}', array(
            'controller' => 'auth-api',
            'action' => 'login'
        ))->via(array('OPTIONS', 'POST'));

        $this->add('/logout', array(
            'controller' => 'auth-api',
            'action' => 'logout'
        ))->via(array('OPTIONS', 'POST'));

        $this->add('/verify-session', array(
            'controller' => 'auth-api',
            'action' => 'verifySession'
        ))->via(array('OPTIONS', 'POST'));

    }

}