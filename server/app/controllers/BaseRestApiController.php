<?php
namespace PhalconSeed\Controllers;

/**
 * Class BaseRestApiController Base class for all REST controllers
 */
class BaseRestApiController extends \Phalcon\Mvc\Controller {

    const HMAC_ALGORITHM = "sha1";
    const JSON = "application/json";
    const HTML = "text/html";
    const NO_CONTENT = "No Content";
    const EXPIRE_TIME = 300; // seconds
    const HEADER_REQUEST_HEADERS = "ACCESS_CONTROL_REQUEST_HEADERS";
    const HEADER_REQUEST_METHOD = "ACCESS_CONTROL_REQUEST_METHOD";
    const HEADER_ALLOW_ORIGIN = "Access-Control-Allow-Origin";
    const HEADER_ALLOW_METHODS = "Access-Control-Allow-Methods";
    const HEADER_ALLOW_HEADERS = "Access-Control-Allow-Headers";
    const HEADER_CONTENT_TYPE = "Content-Type";
    const HEADER_ACCESS_TOKEN = "Access-Token";
    const STATUS_OK = 200;
    const STATUS_CREATED = 201;
    const STATUS_NO_CONTENT = 204;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_FORBIDDEN = 403;
    const STATUS_NOT_FOUND = 404;
    const STATUS_METHOD_NOT_ALLOWED = 405;
    const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
    const STATUS_INTERNAL_ERROR = 500;

    public static $MESSAGES = array(
        self::STATUS_OK => "OK",
        self::STATUS_CREATED => "Created",
        self::STATUS_NO_CONTENT => "No Content",
        self::STATUS_BAD_REQUEST => "Bad Request",
        self::STATUS_UNAUTHORIZED => "Unauthorized",
        self::STATUS_FORBIDDEN => "Forbidden",
        self::STATUS_METHOD_NOT_ALLOWED => "Method Not Allowed",
        self::STATUS_UNSUPPORTED_MEDIA_TYPE => "Unsupported Media Type",
        self::STATUS_INTERNAL_ERROR => "Internal Server Error"
    );

    // which action not need logged in
    private $publicAction = array("login", "logout", "verifySession");
    private $authErrAction = array("sendForbiddenStatus", "sendUnauthorizedStatus");

    public function initialize() {
        $this->view->disable(); // Don't render view
    }

    /**
     * Create a Response object with related parameters
     *
     * @param string $content
     * @param int $status
     * @param string $contentType
     * @param null $message
     * @return \Phalcon\Http\Response
     */
    protected function createResponse($content = self::NO_CONTENT, $status = self::STATUS_OK, $contentType = self::JSON, $message = null) {

        $response = new \Phalcon\Http\Response();

        // Allow CORS
        $response->setHeader(self::HEADER_ALLOW_ORIGIN, "*");

        // set new token to protected api
        if (!in_array($this->dispatcher->getActionName(), $this->publicAction)
            && !in_array($this->dispatcher->getActionName(), $this->authErrAction)) {
            $response->setHeader(self::HEADER_ACCESS_TOKEN, $this->session->get("loggedInUser")->access_token);
        }

        if ($message) {
            $response->setStatusCode($status, $message);
        } else {
            $response->setStatusCode($status, self::$MESSAGES[$status]);
        }

        if ($content !== self::NO_CONTENT) {
            $response->setHeader(self::HEADER_CONTENT_TYPE, $contentType);
            if ($contentType == BaseRestApiController::JSON) {
                $response->setJsonContent($content);
            } else {
                $response->setContent($content);
            }
        }

        return $response;
    }

    /**
     * Create an allowed CORS response for OPTIONS request,
     * some client will send OPTIONS request first in a CORS request such as jQuery.ajax
     *
     * @param $request
     * @return \Phalcon\Http\Response
     */
    public function getCrossBrowserResponse($request) {

        $response = $this->createResponse();
        $response->setHeader(self::HEADER_ALLOW_METHODS, $request->getHeader(self::HEADER_REQUEST_METHOD));
        $response->setHeader(self::HEADER_ALLOW_HEADERS, $request->getHeader(self::HEADER_REQUEST_HEADERS));
        return $response;
    }

    /**
     * Get param in the route url
     *
     * Example: If the route url is "/get/{id}" and a user send a request with url is "/get/1".
     *          When getParam("id") was invoked it will return 1.
     *
     * @param $param
     * @param string $filter
     * @return mixed
     */
    protected function getParam($param, $filter = "trim") {
        return $this->dispatcher->getParam($param, $filter);
    }

    /**
     * Get request url query
     *
     * @param $param
     * @return mixed
     */
    protected function getQuery($param) {
        return $this->request->getQuery($param);
    }

    /**
     * Get request header
     *
     * @param $param
     * @return mixed
     */
    protected function getHeader($param) {
        return $this->request->getHeader($param);
    }

    /**
     * Get request body
     *
     * @return mixed
     */
    protected function getBody() {
        return $this->request->getJsonRawBody();
    }

    /**
     * Check if the request is GET
     *
     * @return mixed
     */
    protected function isGet() {
        return $this->request->isGet();
    }

    /**
     * Check if the request is POST
     *
     * @return mixed
     */
    protected function isPost() {
        return $this->request->isPost();
    }

    /**
     * Check if the request is PUT
     *
     * @return mixed
     */
    protected function isPut() {
        return $this->request->isPut();
    }

    /**
     * Check if the request is PATCH
     *
     * @return mixed
     */
    protected function isPatch() {
        return $this->request->isPatch();
    }

    /**
     * Check if the request is DELETE
     *
     * @return mixed
     */
    protected function isDelete() {
        return $this->request->isDelete();
    }

    /**
     * Check if the request is OPTIONS
     *
     * @return mixed
     */
    protected function isOptions() {
        return $this->request->isOptions();
    }
}