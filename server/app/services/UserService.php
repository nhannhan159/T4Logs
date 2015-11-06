<?php
namespace PhalconSeed\Services;

use Phalcon\Mvc\User\Component;

use PhalconSeed\Models\Users;

class UserService extends Component {

    /*
     * Function used to get all users
     *
     * @return all users
     */
    public function getAll() {
        return array('users' => Users::find()->toArray());
    }

    /*
     * Function used to get user's profile
     *
     * @param int $id
     * @return user's profile
     */
    public function getProfile($id) {

        // find user
        if (is_numeric($id) && $user = Users::findFirst($id)) {
            return array('user' => $user);
        }
        return null;
    }

    /*
     * Function used to get user's profile by username
     *
     * @param string $username
     * @return user's profile
     */
    public function getByName($username) {
        $user = Users::findFirst(array(
            'username = :username:',
            'bind' => array('username' => $username)
        ));
        return $user;
    }

    /*
     * Function used to get user's groups list
     *
     * @param int $id
     * @return array user's roles list
     */
    public function getRoles($id) {

        // find user
        if (is_numeric($id) && $user = Users::findFirst($id)) {
            $roles = [];
            foreach ($user->getRoles() as $role) {
                $roles[] = $role->role_name;
            }
            return array('status' => true, 'roles' => $roles);
        }

        return array(
            'status' => false,
            'message' => 'can not find user'
        );
    }

    /*
     * Function used to create new user
     *
     * @param string $username
     * @param string $password
     * @return result
     */
    public function create($username, $password) {


        // validate username
        if (!$username || !is_string($username)
            || !$this->commonService->validateInput($username, 'username')) {
            return array(
                'status' => false,
                'message' => 'username is not valid'
            );
        }

        // validate password
        if (!$password || !is_string($password)
            || !$this->commonService->validateInput($password, 'password')) {
            return array(
                'status' => false,
                'message' => 'password is not valid'
            );
        }

        // create new user
        $user = new Users();
        $user->username = $username;
        $user->password = $this->security->hash($password);

        // save user
        if (!$user->save()) {
            return array(
                'status' => false,
                'message' => $user->getMessages()['0']->getMessage()
            );
        }

        return array(
            'status' => true,
            'user' => $user
        );

    }

    /*
     * Function used to delete user
     *
     * @param int $id
     * @return result
     */
    public function delete($id) {

        // find user to delete
        if (is_numeric($id) && $delUser = Users::findFirst($id)) {

            // delete user
            if (!$delUser->delete()) {
                return array(
                    'status' => false,
                    'message' => $delUser->getMessages()['0']->getMessage()
                );
            }

        } else {
            return array(
                'status' => false,
                'message' => 'can not find user'
            );
        }

        return array(
            'status' => true,
            'message' => 'success deleted'
        );
    }

    /*
     * Function used to change user password
     *
     * @param int $id
     * @param string $oldpass
     * @param string $newpass
     * @return result
     */
    public function changePassword($id, $oldpass, $newpass) {

        // validate new password
        if (!$newpass || !is_string($newpass)
            || !$this->commonService->validateInput($newpass, 'password')) {
            return array(
                'status' => false,
                'message' => 'new password is not valid'
            );
        }

        // find user
        if (is_numeric($id) && $user = Users::findFirst($id)) {
            if ($this->security->checkHash($oldpass, $user->password)) {

                // change password
                $user->password = $this->security->hash($newpass);

                // save user
                if (!$user->save()) {
                    return array(
                        'status' => false,
                        'message' => $user->getMessages()['0']->getMessage()
                    );
                }

            } else {
                return array(
                    'status' => false,
                    'message' => 'old password in not correct'
                );
            }

        } else {
            return array(
                'status' => false,
                'message' => 'can not find user'
            );
        }

        return array(
            'status' => true,
            'message' => 'success changed password'
        );
    }

}