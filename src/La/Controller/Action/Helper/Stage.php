<?php

class La_Controller_Action_Helper_Stage 
    extends Zend_Controller_Action_Helper_Abstract
{
    protected $_session;
    protected $_id;
    
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace('FORM_STAGE');
    }
    
    public function direct()
    {
        return $this;
    }
    
    public function setFreeTabs($name, $tabs)
    {
        if (!strlen((string) $name)) {
            throw new Exception('Nome do estágio obrigatório!');
        }
        
        $session = $this->getSession($name);
        $session['freeTabs'] = $tabs;
        
        $this->setSession($name, $session);
        
        return $this;
    }
    
    public function setData($name, $stage, $data)
    {
        $session = $this->getSession($name);
        $session['data']['stages'][$stage] = $data;

        $this->setSession($name, $session);
        
        return $this;
    }
    
    public function getData($name, $stage)
    {
        $session = $this->getSession($name);
        
        if (isset($session['data']['stages'][$stage])) {
            return $session['data']['stages'][$stage];
        }
        
        return [];
    }
    
    public function setSession($name, $data)
    {
        $name = $this->getStageName($name);    
        
        $this->_session->$name = $data;
        return $this;
    }
    
    public function getSession($name)
    {
        $name = $this->getStageName($name);    
        
        if (isset($this->_session->$name) && $this->_session->$name) {
            return $this->_session->$name;
        }
        
        return [];
    }
    
    public function clearSession($name)
    {
        $name = $this->getStageName($name);        
        unset($this->_session->$name);
    }
    
    public function getStageName($name)
    {
        $id     = $this->getRequest()->getParam('id', 'add');
        $id     = ($id) ? $id : 'add';
        $name   = '_stage_' . $name . '##' . $id;
        
        return $name;
    }
    
    public function getMergedData($name)
    {
        $data       = [];
        $session    = $this->getSession($name);
        $data       = call_user_func_array('array_merge_recursive', $session['data']['stages']);
        $data       = $this->_arrayMergeRecursive($data);
        
        return $data;
    }
    
    protected function _arrayMergeRecursive(array $array, array $out = [])
    {
        foreach($array as $key => $value) {
            
            if (!is_array($value)) {
                $out[$key] = $value;
            } else {
                
                if (preg_match('#id$#is', trim($key))) {
                    $value = array_unique($value);
                    $out[$key] = end($value);
                } else {
                    
                    if ($this->_hasChildren($value)) {
                        $out[$key] = $this->_arrayMergeRecursive($value);
                        continue;
                    }

                    $value              = @array_unique($value);
                    $keysIsNumeric      = is_numeric(implode('', array_keys($value)));
                    $valuesIsNumeric    = is_numeric(implode('', $value));
                    
                    $out[$key] = (count($value) <= 1 || ($keysIsNumeric && !$valuesIsNumeric)) ? reset($value) : $value;
                }
                
            }
            
        }
        
        return $out;
    }
    
    protected function _hasChildren(array $array)
    {
        foreach($array as $key => $value) {
            if (is_array($value)) {
                return true;
            }
        }
        
        return false;    
    }
}