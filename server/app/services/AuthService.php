<?php
namespace PhalconSeed\Services;

use Phalcon\Mvc\User\Component;
use Firebase\JWT\JWT as JWT;

use PhalconSeed\AppConstants\GlobalConstant;
use PhalconSeed\Models\Users;

class AuthService extends Component {

    /*
     * Function used to verify user's login.
     *
     * @param string $username
     * @param string $password
     * @return result
     */
    public function login($username, $password) {

        // find user by name
        if ($user = $this->userService->getByName($username)) {

            // check password
            if ($this->security->checkHash($password, $user->password)) {

                // save user's new token
                $jwt = $this->generateToken($user);
                $user->access_token = $jwt;
                $user->last_login = date('Y-m-d H:i:s', time());
                $user->expire_time = date('Y-m-d H:i:s', time() + GlobalConstant::SESSION_EXP);
                $user->batch_time = date('Y-m-d H:i:s', time());
                $user->save();

            } else {
                return array(
                    'status' => false,
                    'message' => 'password is not valid'
                );
            }

        } else {
            return array(
                'status' => false,
                'message' => 'username is not valid'
            );
        }

        $this->session->set('loggedInUser', $user);
        $this->actionLogger->info("$user->username: logged in (local)");
        return array(
            'status' => true,
            'token' => $jwt
        );
    }

    /*
     * Function used to verify user's login.
     *
     * @param string $username
     * @param string $password
     * @param string $sessionId
     * @return result
     */
    public function loginLDAP($username, $password) {

        $adldap = $this->ldap;
        $adldap->setAccountSuffix('');
        $adldap->setDomainControllers(array($this->config->application->ldapDomain));
        $adldap->setBaseDn($this->config->application->ldapBaseDn);

        // connect to ldap's server
        if ($adldap->connect()) {

            // for testing
            $testUsername = 'uid=' . $username . ',dc=example,dc=com';

            // authenticate
            if ($adldap->authenticate($testUsername, $password)) {

                // check if use is existed
                if ($user = $this->userService->getByName($username)) {

                    // update user's password
                    $user->password = $this->security->hash($password);
                    $user->save();

                } else {
                    // create new user
                    $createdResult = $this->userService->create($username, $password);
                    if (!$createdResult['status']) {
                        return $createdResult;
                    }
                }

                // save new session
                $user = $this->userService->getByName($username);
                $jwt = $this->generateToken($user);
                $user->access_token = $jwt;
                $user->last_login = date('Y-m-d H:i:s', time());
                $user->expire_time = date('Y-m-d H:i:s', time() + GlobalConstant::SESSION_EXP);
                $user->batch_time = date('Y-m-d H:i:s', time());
                $user->save();

                $this->session->set('loggedInUser', $user);
                $this->actionLogger->info("$username: logged in (ldap)");
                return array(
                    'status' => true,
                    'token' => $jwt
                );

            }

        }
        return array(
            'status' => false,
            'message' => 'some thing wrong'
        );
    }

    /*
     * Function used to logout user.
     *
     * @param string $token
     * @return mixed
     */
    public function logout($token) {

        // remove user's token
        if ($user = $this->getLoggedInUser($token)) {
            $user->access_token = null;
            $user->batch_token = null;
            $user->save();
            $this->actionLogger->info("$user->username: logged out");
            return true;
        }
        return false;
    }

    /*
     * Function used to verify user's session.
     *
     * @param string $token
     * @param boolean $renew
     * @return true if user has session
     */
    public function verifySession($token, $renew) {

        // get logged in user exist
        if ($user = $this->getLoggedInUser($token)) {

            // create new token
            if (strtotime($user->batch_time) <= time()) {
                $jwt = $this->generateToken($user);
                $user->batch_token = $user->access_token;
                $user->access_token = $jwt;
                $user->batch_time = date('Y-m-d H:i:s', time() + GlobalConstant::BATCH_REQUESTS_TIME);

            } else { // use old token for batch requests
                $jwt = $user->access_token;
            }

            // renew expired time
            if ($renew) {
                $user->expire_time = date('Y-m-d H:i:s', time() + GlobalConstant::SESSION_EXP);
            }
            $user->save();
            $this->session->set('loggedInUser', $user);
            return $jwt;
        }
        return false;
    }

    /*
     * Function used to get logged-in user by token
     *
     * @param string $token
     * @return user
     */
    public function getLoggedInUser($token) {

        try {
            // decode token
            $tokenBody = JWT::decode($token, GlobalConstant::SECRET_KEY, array('HS256'));

            // get user
            $user = Users::findFirst(array(
                'id = :id: AND access_token = :token: AND expire_time > :now:',
                'bind' => array(
                    'id' => $tokenBody->id,
                    'token' => $token,
                    'now' => date('Y-m-d H:i:s', time())
                )
            ));

            // find in batch token
            if (!$user) {
                $user = Users::findFirst(array(
                    'id = :id: AND batch_token = :token: AND expire_time > :now: AND batch_time > :now:',
                    'bind' => array(
                        'id' => $tokenBody->id,
                        'token' => $token,
                        'now' => date('Y-m-d H:i:s', time())
                    )
                ));
            }

            return $user;

        } catch (\Exception $ex) {
            return null;
        }

    }

    /*
     * Function used to generate login token to send to client
     *
     * @param int $userId
     * @return login token
     */
    private function generateToken($user) {
        $tokenBody = array(
            'jti' => GlobalConstant::unique_md5(),
            'id' => $user->id,
            'username' => $user->username,
            'tokenCreateAt' => date('Y-m-d H:i:s', time())
        );

        $jwt = JWT::encode($tokenBody, GlobalConstant::SECRET_KEY);
        $this->actionLogger->info("$user->username: generate token: $jwt");
        return $jwt;
    }

}