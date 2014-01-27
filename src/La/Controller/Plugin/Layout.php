<?php

class La_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $layout = Zend_Controller_Action_HelperBroker::getStaticHelper('Layout');
        $view   = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        $layoutParam = $request->getParam('layout');
        
        if ($layoutParam) {
            $layout->setLayout($layoutParam);
        }
        
        $identity = Zend_Auth::getInstance()->getIdentity();
        
        if (isset($identity->role)) {
            $view->identity = $identity;
        }
        
        $view->selectId = $request->getParam('selectId');
        $view->table = $request->getParam('table');
        $view->menu  = $request->getParam('table');
    }
}