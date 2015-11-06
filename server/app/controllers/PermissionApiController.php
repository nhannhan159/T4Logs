<?php
namespace PhalconSeed\Controllers;

use Phalcon\Http\Response;

class PermissionApiController extends BaseRestApiController {

    public function getResourcesAction() {

        try {
            $resources = $this->acl->getResources();
            return $this->createResponse(array('resources' => $resources));
        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][getResourcesAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function getPermissionsAction() {

        try {
            $role_name = $this->getParam('role_name', 'string');
            $permissions = $this->acl->getPermissions($role_name);
            if (!$role_name) {
                return $this->createResponse(array("message" => 'role name is not found'), self::STATUS_BAD_REQUEST);
            }
            return $this->createResponse($permissions);
        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][getResourcesAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

    public function assignAction() {

        try {
            $permissions = $this->getBody();
            $this->acl->create($permissions);
            $this->acl->rebuild();
            return $this->createResponse(array("message" => 'success assign new permissions'), self::STATUS_CREATED);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][assignAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

}