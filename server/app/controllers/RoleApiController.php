<?php
namespace PhalconSeed\Controllers;

use Phalcon\Http\Response;

class RoleApiController extends BaseRestApiController {

    public function getAllAction() {

        try {
            $responseObject = $this->roleService->getAll();
            return $this->createResponse($responseObject);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][getAllAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function getUsersAction() {

        try {
            $role = $this->getParam("role_name", "string");
            $responseObject = $this->roleService->getUsers($role);
            if ($responseObject['status']) {
                return $this->createResponse(array('users' => $responseObject['users']));
            }
            return $this->createResponse(array('message' => $responseObject['message']), self::STATUS_BAD_REQUEST);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][getUsersAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function createAction() {

        try {
            $role = $this->getParam("role_name", "string");
            $responseObject = $this->roleService->create($role);
            if ($responseObject["status"]) {
                return $this->createResponse(array('role' => $responseObject['role']), self::STATUS_CREATED);
            }
            return $this->createResponse(array("message" => $responseObject["message"]), self::STATUS_BAD_REQUEST);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][createAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function deleteAction() {

        try {
            $role = $this->getParam("role_name", "string");
            $responseObject = $this->roleService->delete($role);
            if ($responseObject["status"]) {
                return $this->createResponse(array("message" => $responseObject["message"]));
            }
            return $this->createResponse(array("message" => $responseObject["message"]), self::STATUS_BAD_REQUEST);

        } catch (Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][deleteAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function assignAction() {

        try {
            $role = $this->getParam("role_name", "string");
            $username = $this->getParam("username", "string");
            $responseObject = $this->roleService->assign($role, $username);
            if ($responseObject["status"]) {
                return $this->createResponse(array("message" => $responseObject["message"]), self::STATUS_CREATED);
            }
            return $this->createResponse(array("message" => $responseObject["message"]), self::STATUS_BAD_REQUEST);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][assignUsersToGroupAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

}