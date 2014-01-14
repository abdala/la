<?php

class La_Controller_Plugin_StaticLogin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $session    = new Zend_Session_Namespace('login');
        $view       = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        $module     = $request->getModuleName();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        
        if (!$session->logged) {
            if ($module == 'default' 
                && ($controller == 'error' || $controller == 'login')) 
            {
                return true;
            }
            
            $request->setModuleName('default')
                    ->setControllerName('login')
                    ->setActionName('index');
            
            return false;
        }
        
        $view->logged = $session->logged;
        $view->module = $module;
        $view->controller = $controller;
        $view->action = $action;
    }
}