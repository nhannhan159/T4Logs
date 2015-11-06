<?php
namespace PhalconSeed\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;

/**
 * Security
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class Security extends Plugin {

    public function __construct($dependencyInjector) {
        $this->_dependencyInjector = $dependencyInjector;
    }

    /**
     * This action is executed before execute any action in the application
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher) {

        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        // Prevent loop
        if ($controller == 'quick-response') {
            return true;
        }

        // handle option method
        if ($this->request->isOptions()) {
            $this->dispatcher->forward(array(
                'controller' => 'quick-response',
                'action' => 'sendCrossBrowserResponse'
            ));
            return true;
        }

        // secure protected action
        if ($this->acl->isPrivate($controller, $action)) {

            // get access token
            $accessToken = $this->request->getHeader('Access-Token');

            // check session
            if (!$accessToken || !($newToken = $this->authService->verifySession($accessToken, false))) {
                $this->actionLogger->info('anonymous: authentication fail');
                $this->dispatcher->forward(array(
                    'controller' => 'quick-response',
                    'action' => 'sendForbiddenStatus'
                ));
                return false;
            }

            // logging valid session
            $this->actionLogger->info($this->session->get('loggedInUser')->username . ': authentication success');

            // check accessible
            if (!$this->acl->isAllowed($this->session->get('loggedInUser'), $controller, $action)) {
                $this->actionLogger->info($this->session->get('loggedInUser')->username .
                    "authorization fail: try to access $controller.$action");
                $this->dispatcher->forward(array(
                    'controller' => 'quick-response',
                    'action' => 'sendUnauthorizedStatus'
                ));
                return false;
            }

            // logging valid accessible
            $this->actionLogger->info($this->session->get('loggedInUser')->username .
                ": authorization success: try to access $controller.$action");
        }

        return true;
    }

    /**
     * Triggered before the dispatcher throws any exception
     */
    public function beforeException(Event $event, Dispatcher $dispatcher, $exception) {

        echo 'testing';
        // Handle 404 exceptions
        if ($exception instanceof DispatchException) {
            $dispatcher->forward(array(
                'controller' => 'quick-response',
                'action'     => 'sendNotFoundStatus'
            ));
            return false;
        }

        // Alternative way, controller or action doesn't exist
        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(array(
                        'controller' => 'quick-response',
                        'action'     => 'sendNotFoundStatus'
                    ));
                    return false;
            }
        }

        return true;
    }

}
