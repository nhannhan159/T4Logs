<?php
namespace PhalconSeed\Controllers;

use Phalcon\Http\Response;

class UserApiController extends BaseRestApiController {

    public function getAllAction() {

        try {
            $users = $this->userService->getAll();
            return $this->createResponse($users);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][getAllAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function getByIdAction() {

        try {
            $userId = $this->getParam('id', 'int');
            $profile = $this->userService->getById($userId);
            return $this->createResponse($profile);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][getByIdAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function getRolesAction() {

        try {
            $userId = $this->getParam('id', 'int');
            $responseObject = $this->userService->getRoles($userId);
            if ($responseObject['status']) {
                return $this->createResponse(array('roles' => $responseObject['roles']));
            }
            return $this->createResponse(array('message' => $responseObject['message']), self::STATUS_BAD_REQUEST);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][getRolesAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function createAction() {

        try {
            $user = $this->getBody();
            $responseObject = $this->userService->create($user->username, $user->password);
            if ($responseObject['status']) {
                return $this->createResponse(array('user' => $responseObject['user']), self::STATUS_CREATED);
            }
            return $this->createResponse(array('message' => $responseObject['message']), self::STATUS_BAD_REQUEST);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][createAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function deleteAction() {

        try {
            $userId = $this->getParam('id', 'int');
            $responseObject = $this->userService->delete($userId);
            if ($responseObject['status']) {
                return $this->createResponse(array('message' => $responseObject['message']));
            }
            return $this->createResponse(array('message' => $responseObject['message']), self::STATUS_BAD_REQUEST);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][deleteAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function changePasswordAction() {

        try {
            $userId = $this->getParam('id', 'int');
            $putObject = $this->getBody();
            $responseObject = $this->userService->changePassword($userId, $putObject);
            if ($responseObject['status']) {
                return $this->createResponse(array('message' => $responseObject['message']));
            }
            return $this->createResponse(array('message' => $responseObject['message']), self::STATUS_BAD_REQUEST);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][changePasswordAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

}