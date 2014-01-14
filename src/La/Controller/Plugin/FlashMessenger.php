<?php
    
class La_Controller_Plugin_FlashMessenger extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $view  = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        
        $view->messages = NULL;
        
        if ($flash->hasMessages()) {
            $view->messages = $flash->getMessages();
        }
    }
}