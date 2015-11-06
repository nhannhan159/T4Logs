<?php
namespace PhalconSeed\Controllers;

use Phalcon\Http\Response;

class AuthApiController extends BaseRestApiController {

    public function loginAction() {

        try {
            $method = $this->getParam("method", "string");
            $postObject = $this->getBody();

            // validate input
            if (!in_array($method, array("local", "ldap"))) {
                $msg = array(
                    "message" => implode(".", array(
                        AuthService::ERROR,
                        AuthService::OBJECT_USER,
                        AuthService::FIELD_USERNAME,
                        AuthService::ERROR_TYPE_INVALID
                    )));
                return $this->createResponse($msg, self::STATUS_BAD_REQUEST);
            }

            $username = $postObject->username;
            $password = $postObject->password;

            switch ($method) {
                case "local":
                    $responseObject = $this->authService->login($username, $password);
                    break;
                case "ldap":
                    $responseObject = $this->authService->loginLDAP($username, $password);
                    break;
                default:
                    $responseObject = null;
            }

            if ($responseObject["status"]) {
                $response = $this->createResponse(array(
                    "message" => 'success log in'
                ));
                $response->setHeader(self::HEADER_ACCESS_TOKEN, $responseObject["token"]);
                return $response;
            }
            $this->actionLogger->info("anonymous: login fail: username = '$username', password = '$password'");
            return $this->createResponse(array("message" => $responseObject["message"]), self::STATUS_BAD_REQUEST);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][loginAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function logoutAction() {

        try {
            $accessToken = $this->getHeader(self::HEADER_ACCESS_TOKEN);
            if ($this->authService->logout($accessToken)) {
                $response = $this->createResponse(array("message" => 'success log out'), self::STATUS_OK);
            } else {
                $response = $this->createResponse(self::NO_CONTENT, self::STATUS_FORBIDDEN);
            }
            return $response;

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][logoutAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function verifySessionAction() {

        try {
            $accessToken = $this->getHeader(self::HEADER_ACCESS_TOKEN);
            if ($newToken = $this->authService->verifySession($accessToken, true)) {
                $response = $this->createResponse(array(
                    "message" => implode(".", array(
                        AuthService::SUCCESS, AuthService::OBJECT_USER, AuthService::ACTION_UPDATESESSION
                    ))
                ));
                $response->setHeader(self::HEADER_ACCESS_TOKEN, $newToken);
                return $response;
            }
            return $this->createResponse(self::NO_CONTENT, self::STATUS_FORBIDDEN);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][verifySessionAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

}