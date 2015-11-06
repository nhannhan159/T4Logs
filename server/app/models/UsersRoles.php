<?php
namespace PhalconSeed\Models;

use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\Validator\Uniqueness as Uniqueness;
use Phalcon\Mvc\Model\Validator\Regex as RegexValidator;

use PhalconSeed\AppConstants\GlobalConstant;

class UsersRoles extends \Phalcon\Mvc\Model {

    public $user_id; // int(11) NOT NULL,
    public $role_id; // int(11) NOT NULL,

    public function initialize()
    {
        $this->belongsTo('user_id', '\PhalconSeed\Models\Users', 'id', array('alias' => 'User'));
        $this->belongsTo('role_id', '\PhalconSeed\Models\Roles', 'id', array('alias' => 'Role'));
    }

}