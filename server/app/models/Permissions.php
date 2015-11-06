<?php
namespace PhalconSeed\Models;

class Permissions extends \Phalcon\Mvc\Model {

    public $id; // int(9) NOT NULL AUTO_INCREMENT, PK,
    public $role_id; // int(9) NOT NULL,
    public $controller; // varchar(20) NOT NULL,
    public $action; // varchar(20) NOT NULL,

    public function initialize()
    {
        $this->belongsTo('role_id', '\PhalconSeed\Models\Roles', 'id', array('alias' => 'Role'));
    }
}