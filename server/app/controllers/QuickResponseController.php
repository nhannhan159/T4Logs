<?php
namespace PhalconSeed\Controllers;

use Phalcon\Http\Response;

class QuickResponseController extends BaseRestApiController {

    /**
     * Action used to send cross browser response
     */
    public function sendCrossBrowserResponseAction() {
        return $this->getCrossBrowserResponse($this->request);
    }

    /**
     * Action used to send forbidden status
     */
    public function sendForbiddenStatusAction() {
        return $this->createResponse(self::NO_CONTENT, self::STATUS_FORBIDDEN);
    }

    /**
     * Action used to send unauthorized status
     */
    public function sendUnauthorizedStatusAction() {
        return $this->createResponse(self::NO_CONTENT, self::STATUS_UNAUTHORIZED);
    }

    /**
     * Action used to send method not allowed status
     */
    public function sendMethodNotAllowedStatusAction() {
        return $this->createResponse(self::NO_CONTENT, self::STATUS_METHOD_NOT_ALLOWED);
    }

    /**
     * Action used to send not found status
     */
    public function sendNotFoundStatusAction() {
        return $this->createResponse(self::NO_CONTENT, self::STATUS_NOT_FOUND);
    }

}