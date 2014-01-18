<?php

class La_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    const RESOURCE_SEPARATOR = ":";
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $table      = $request->getParam('table');
        $acl        = $this->_getAcl();
        $identity   = Zend_Auth::getInstance()->getIdentity();
        $role       = 'Todos';
        $resource   = strtolower($request->getModuleName());
        $controller = $request->getControllerName();
        $privilege  = $controller
                    . self::RESOURCE_SEPARATOR
                    . $request->getActionName();
        
        if (isset($identity->role)) {
            $role = $identity->role;
        }
        
        if (!$acl->isAllowed($role, $resource, $privilege)) {
            if ($controller == 'scaffold' && $table) {
                $privilege = $table
                           . self::RESOURCE_SEPARATOR
                           . $request->getActionName();
                if ($acl->isAllowed($role, $resource, $privilege)) {
                    return true;
                }
            }
            
            if (isset($identity->role)) {
                $request->setModuleName('default')
                        ->setControllerName('error')
                        ->setActionName('access');
                return false;
            }
            
            $request->setModuleName('auth')
                    ->setControllerName('index')
                    ->setActionName('index');
        }
    }
    
    protected function _getAcl()
    {
        $acl = false;
        
        if (Zend_Registry::isRegistered('cache')) {
            $cache = Zend_Registry::get('cache');
            $acl   = $cache->load('acl');
        }
        
        if (!$acl) {
            $acl          = new Zend_Acl();
            $role         = new Auth_Model_DbTable_Role();
            $resource     = new Auth_Model_DbTable_Resource();
            $roleResource = new Auth_Model_DbTable_RoleResource();
            
            $roles     = $role->fetchAll("name <> 'Todos'");
            $resources = $resource->getDistinctModules(); 
            $relations = $roleResource->fetchAllRelations();
            
            $acl->addRole('Todos');
            
            foreach ($roles as $role) {
                $acl->addRole($role['name'], 'Todos');
            }
            
            foreach ($resources as $resource) {
                $acl->addResource($resource['module']);              
            }
            
            foreach ($relations as $relation) {
                $acl->allow($relation['name'], $relation['module'], $relation['privilege']);
            }
            
            if (Zend_Registry::isRegistered('cache')) {
                $cache->save($acl, 'acl');
            }
        }
        
        Zend_Registry::set('acl', $acl);
        
        return $acl;
    }
}