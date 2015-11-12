<?php
namespace PhalconSeed\Controllers;

use Phalcon\Http\Response;

class LogApiController extends BaseRestApiController {

    public function getAllAction() {

        try {
            $responseObject = $this->logService->getAll();
            if ($responseObject['statusCode'] == self::STATUS_OK) {
                return $this->createResponse($responseObject['body'], self::STATUS_OK, '');
            }
            return $this->createResponse(self::NO_CONTENT, $responseObject['statusCode']);

        } catch (\Exception $ex) {
            $this->crashLogger->error('[EXCEPTION][getAllAction]: ' . $ex->getMessage());
            return $this->createResponse(self::NO_CONTENT, self::STATUS_INTERNAL_ERROR);
        }
    }

}