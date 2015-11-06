<?php
namespace PhalconSeed\Services;

use Phalcon\Mvc\User\Component;

use PhalconSeed\Models\Roles;

class RoleService extends Component {

    /*
     * Function used to get all groups
     *
     * @return all group
     */
    public function getAll() {
        return array('roles' => Roles::find()->toArray());
    }

    /*
     * Function used to get role by name
     *
     * @param string $name
     * @return user's profile
     */
    public function getByName($name) {
        $role = Roles::findFirst(array(
            'name = :name:',
            'bind' => array('name' => $name)
        ));
        return $role;
    }

    /*
     * Function used to get group of user by role name
     *
     * @param string $name
     * @return list of users in group
     */
    public function getUsers($name) {

        // validate role name
        $role = Roles::findFirst(array(
            'name = :name:',
            'bind' => array('name' => $name)
        ));

        if ($role) {
            return array(
                "status" => true,
                "users" => $role->getUsers()->toArray()
            );
        }

        return array(
            'status' => false,
            'message' => 'role not found'
        );
    }

    /*
     * Function used to create role
     *
     * @param string $name
     * @return result
     */
    public function create($name) {

        // validate role's name
        if (!$name || !$this->commonService->validateInput($name, "role_name")) {
            return array(
                "status" => false,
                "message" => "role's name is not valid"
            );
        }

        // create new role
        $role = new Roles();
        $role->name = $name;
        if (!$role->save()) {
            return array(
                "status" => false,
                "message" => $role->getMessages()['0']->getMessage()
            );
        }

        return array(
            "status" => true,
            "role" => $role
        );
    }

    /*
     * Function used to delete group
     *
     * @param string $groupName
     * @return result
     */
    public function delete($role_name) {

        // validate role name
        if ($role = Roles::findFirst($role_name)) {
            $role->delete();
        }

        return array(
            'status' => false,
            'message' => 'role not found'
        );
    }

    /*
     * Function used to assign users to group
     *
     * @param string $username
     * @param string $role
     * @return result
     */
    public function assign($role, $username) {

        if ($role = $this->getByName($role)) {
            if ($user = $this->userService->getByName($username)) {
                $usersInRoles = $role->getUsers()->toArray();
                $usersInRoles[] = $user;
                $role->users = $usersInRoles;
                if (!$role->save()) {
                    return array(
                        'status' => false,
                        'message' => $role->getMessages()['0']->getMessage()
                    );
                }
            } else {
                return array(
                    'status' => false,
                    'message' => 'user not found'
                );
            }
        } else {
            return array(
                'status' => false,
                'message' => 'role not found'
            );
        }

        return array(
            'status' => true,
            'message' => 'success assign'
        );
    }

}