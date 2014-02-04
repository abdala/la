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
        
        if ($acl && !$acl->isAllowed($role, $resource, $privilege)) {
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
        if (Zend_Registry::isRegistered('acl')) {
            return Zend_Registry::get('acl');
        }
        
        return false;
    }
}