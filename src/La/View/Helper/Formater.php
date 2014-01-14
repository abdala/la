<?php

class La_View_Helper_Formater extends Zend_View_Helper_Abstract
{

    protected $helpers;

    public function formater(array $helpers)
    {
        $this->setHelpers($helpers);
        return $this;
    }

    public function setHelpers(array $helpers)
    {
        $this->helpers = $helpers;
        return $this;
    }

    public function getHelperByKey($key)
    {
        if( isset($this->helpers[$key]) ){
            return $this->helpers[$key];
        }
        return false;
    }

    public function cnpj($field, $data = null)
    {
        $value = $field;
        if (isset($data[$field])) {
            $value = $data[$field];
        }
        if (strlen($value) > 11) {
            return $this->view->mask($value, '##.###.###/####-##');
        }  else {
            return $this->view->mask($value, '###.###.###-##');
        }
    }
    
    public function phone($field, $data = null)
    {
        $value = $field;
        if (isset($data[$field])) {
            $value = $data[$field];
        }
        
        $strlen = strlen($value);
        
        if (strlen($value) == 8) {
            return $this->view->mask($value, '####-####');
        } elseif (strlen($value) == 9) {
            return $this->view->mask($value, '#####-####');
        } elseif (strlen($value) == 10) {
            return $this->view->mask($value, '(##) ####-####');
        } elseif (strlen($value) == 11) {
            return $this->view->mask($value, '(##) #####-####');
        } 
        
        return $value;
    }

    public function date($field, $data = null)
    {
        $value = $field;
        if (isset($data[$field])) {
            $value = $data[$field];
        }

        $filter = new La_Filter_Date();
        return $filter->filter($value);
    }
    
    public function datetime($field, $data = null)
    {
        $value = $field;
        if (isset($data[$field])) {
            $value = $data[$field];
        }

        $filter = new La_Filter_Datetime();
        return $filter->filter($value);
    }

    public function chaveNfe($field, $data = null)
    {
        $value = $field;
        if (isset($data[$field])) {
            $value = $data[$field];
        }

        return $this->view->mask($value,'#### #### #### #### #### #### #### #### ####');
    }
    
    public function money($field, $data = null)
    {
        $value = $field;
        
        if (isset($data[$field])) {
            $value = $data[$field];
        }

        return number_format($value, 2, ',', '.');
    }
}