<?php

class La_Controller_Plugin_StaticLogin extends Zend_Controller_Plugin_Abstract
{
    const RESOURCE_SEPARATOR = ":";
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $session = new Zend_Session_Namespace('login');
        
        if (!$session->logged) {
            $module = $request->getModuleName();
            $controller = $request->getControllerName();

            if ($module == 'api' || $module == 'auth' || ($module == 'default' && $controller == 'error')) {
                return true;
            }
            
            $request->setModuleName('auth')
                    ->setControllerName('index')
                    ->setActionName('index');
        }
    }
}