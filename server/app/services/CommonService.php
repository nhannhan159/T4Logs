<?php
namespace PhalconSeed\Services;

use Phalcon\Mvc\User\Component;

use PhalconSeed\AppConstants\GlobalConstant;

class CommonService extends Component {

    const STATUS_SUCCESS = "success";
    const STATUS_ERROR = "error";

    /*
     * Function used to validate user supplied variables.
     */
    function validateInput($value, $regExpName) {

        if (!GlobalConstant::$regExpArray[$regExpName]) {
            return false;
        }

        $regExp = GlobalConstant::$regExpArray[$regExpName];
        if (is_array($value)) {
            foreach ($value as $arrval) {
                if (!preg_match($regExp, $arrval)) {
                    return false;
                }
            }
        } elseif (!preg_match($regExp, $value)) {
            return false;
        }
        return true;
    }

}