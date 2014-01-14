<?php

class La_Form_SubForm extends La_Form
{
    protected $_isArray = true;
    
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->removeDecorator('Form');
        $this->removeDecorator('Validator');
        $this->removeElement('Enviar');
    } 
}