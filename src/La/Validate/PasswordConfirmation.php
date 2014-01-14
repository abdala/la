<?php

class La_Validate_PasswordConfirmation extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Senhas não conferem.'
    );

    public function isValid($value, $context = null)
    {   
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context) 
            && isset($context['password_confirmacao']) 
            && ($value == $context['password_confirmacao']))
        {
            return true;
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}