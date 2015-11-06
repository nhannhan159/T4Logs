<?php
namespace PhalconSeed\Models;

use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\Regex as RegexValidator;

use PhalconSeed\AppConstants\GlobalConstant;

class Roles extends \Phalcon\Mvc\Model {

    public $id; // int(11) NOT NULL AUTO_INCREMENT, PK,
    public $name; // varchar(20) NOT NULL,

    public function initialize()
    {
        $this->hasManyToMany(
            'id',
            '\PhalconSeed\Models\UsersRoles',
            'role_id', 'user_id',
            '\PhalconSeed\Models\Users',
            'id',
            array('alias' => 'Users')
        );
        $this->hasMany(
            'id',
            '\PhalconSeed\Models\Permissions',
            'role_id',
            array('alias' => 'Permissions')
        );
    }

    public function validation()
    {
        $this->validate(new Uniqueness(array(
            'field'   => 'name',
            'message' => "role's name is unique"
        )));
        $this->validate(new RegexValidator(array(
            'field' => 'name',
            'pattern' => GlobalConstant::$regExpArray['role_name'],
            'message' => "role's name is not valid"
        )));

        return $this->validationHasFailed() != true;
    }

}