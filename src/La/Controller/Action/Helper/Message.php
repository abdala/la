<?php

class La_Controller_Action_Helper_Message
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * 
     * @param string $key
     * @return array
     */
    public function direct($key, $useFlashMessenger = true)
    {
        $messages = $this->getActionController()->getInvokeArg('bootstrap')->getOption('messages');
        
        if (isset($messages[$key])) {
            $message = array(key($messages[$key]) => current($messages[$key]));
            
            if ($useFlashMessenger) { 
                $this->getActionController()->getHelper('flashMessenger')->addMessage($message);
            }
            
            return $message;
        }
        
        return null;
    }
}