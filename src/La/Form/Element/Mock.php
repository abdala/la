<?php

class La_Form_Element_Mock extends Zend_Form_Element
{
    public function __call($method, $args)
    {
        return $this;
    }
    
    public function render(Zend_View_Interface $view = NULL)
    {
        return '';
    }
    
    public function __toString()
    {
        return '';
    }
}
