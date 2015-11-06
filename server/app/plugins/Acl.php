<?php
namespace PhalconSeed\Plugins;

use Phalcon\Mvc\User\Component;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Role as AclRole;
use Phalcon\Acl\Resource as AclResource;

use PhalconSeed\AppConstants\GlobalConstant;
use PhalconSeed\Models\Permissions;
use PhalconSeed\Models\Roles;

class Acl extends Component
{

    /**
     * The ACL Object
     *
     * @var \Phalcon\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * The filepath of the ACL cache file from APP_DIR
     *
     * @var string
     */
    private $filePath = '/cache/acl/data.txt';

    /**
     * Define the resources that are considered "private". These controller => actions require authentication.
     *
     * @var array
     */
    private $privateResources = array(
        'user-api' => array('getAll', 'getById', 'getRoles', 'create', 'delete', 'changePassword'),
        'role-api' => array('getAll', 'getUsers', 'create', 'delete', 'assign')
    );

    /**
     * Checks if a controller is private or not
     *
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isPrivate($controller, $action)
    {
        return isset($this->privateResources[$controller])
            && in_array($action, $this->privateResources[$controller]);
    }

    /**
     * Checks if the current profile is allowed to access a resource
     *
     * @param object $user
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isAllowed($user, $controller, $action)
    {
        if ($user->username === GlobalConstant::SUPER_ADMIN) { return true;}
        foreach ($user->getRoles() as $role) {
            if ($this->getAcl()->isAllowed($role->name, $controller, $action)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the ACL list
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function getAcl()
    {

        // Check if the ACL is already created
        if (is_object($this->acl)) {
            return $this->acl;
        }

        // Check if the ACL is in APC
        if (function_exists('apc_fetch')) {
            $acl = apc_fetch('phalconseed-acl');
            if (is_object($acl)) {
                $this->acl = $acl;
                return $acl;
            }
        }


        // Check if the ACL is already generated
        if (!file_exists(APP_DIR . $this->filePath)) {
            $this->acl = $this->rebuild();
            return $this->acl;
        }

        // Get the ACL from the data file
        $data = file_get_contents(APP_DIR . $this->filePath);
        $this->acl = unserialize($data);

        // Store the ACL in APC
        if (function_exists('apc_store')) {
            apc_store('phalconseed-acl', $this->acl);
        }

        return $this->acl;
    }

    /**
     * Returns the permissions assigned to a profile
     *
     * @param string $role_name
     * @return array
     */
    public function getPermissions($role_name)
    {
        if ($role = $this->roleService->getByName($role_name)) {
            $permissions = array();
            foreach ($role->getPermissions() as $permission) {
                $permissions[] = $permission->controller . '.' . $permission->action;
            }
            return $permissions;
        }
        return false;
    }

    /**
     * Returns all the resources and their actions available in the application
     *
     * @return array
     */
    public function getResources()
    {
        return $this->privateResources;
    }

    /**
     * Create new permissions
     *
     * @return mixed
     */
    public function create($permissions)
    {
        //example objects: [ "3.user-api.getall", "4.role-api.create" ]
        foreach ($permissions as $permission) {
            try {
                $newPerm = new Permissions();
                $permission = explode('.', $permission);
                $newPerm->role_id = $permission[0];
                $newPerm->controller = $permission[1];
                $newPerm->action = $permission[2];
                $newPerm->save();
            } catch (\Exception $e) {}
        }
        return $this->acl;
    }

    /**
     * Rebuilds the access list into a file
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function rebuild()
    {
        $acl = new AclMemory();

        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        // Register roles
        $roles = Roles::find();

        foreach ($roles as $role) {
            $acl->addRole(new AclRole($role->name));
        }

        foreach ($this->privateResources as $resource => $actions) {
            $acl->addResource(new AclResource($resource), $actions);
        }

        // Grant acess to private area to role Users
        foreach ($roles as $role) {

            // Grant permissions in "permissions" model
            foreach ($role->getPermissions() as $permission) {
                $acl->allow($role->name, $permission->controller, $permission->action);
            }

            // Always grant these permissions
            foreach ($this->privateResources as $controller => $actions) {
                foreach ($actions as $action) {
                    $acl->allow('admins', $controller, $action);
                }
            }
        }

        if (touch(APP_DIR . $this->filePath) && is_writable(APP_DIR . $this->filePath)) {

            file_put_contents(APP_DIR . $this->filePath, serialize($acl));
            chmod(APP_DIR . $this->filePath, 0777);

            // Store the ACL in APC
            if (function_exists('apc_store')) {
                apc_store('phalconseed-acl', $acl);
            }
        } else {
            $this->flash->error(
                'The user does not have write permissions to create the ACL list at ' . APP_DIR . $this->filePath
            );
        }

        return $acl;
    }
}
