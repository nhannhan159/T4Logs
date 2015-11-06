<?php
namespace PhalconSeed\Models;

use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Regex as RegexValidator;

use PhalconSeed\AppConstants\GlobalConstant;

class Users extends \Phalcon\Mvc\Model {

    public $id; // int(11) NOT NULL AUTO_INCREMENT, PK,
    public $username; // varchar(20) NOT NULL,
    public $password; // char(65) NOT NULL,
    public $last_login; // datetime,
    public $expire_time; // datetime,
    public $access_token; // char(250),
    public $batch_token; // char(250),
    public $batch_time; // datetime,

    public function initialize()
    {
        $this->hasManyToMany(
            'id',
            '\PhalconSeed\Models\UsersRoles',
            'user_id', 'role_id',
            '\PhalconSeed\Models\Roles',
            'id',
            array('alias' => 'Roles')
        );
    }

    public function validation()
    {
        $this->validate(new Uniqueness(array(
            'field'   => 'username',
            'message' => 'username is unique'
        )));
        $this->validate(new RegexValidator(array(
            'field' => 'username',
            'pattern' => GlobalConstant::$regExpArray['username'],
            'message' => 'username is not valid'
        )));
        return $this->validationHasFailed() != true;
    }

}