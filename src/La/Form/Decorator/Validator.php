<?php
class La_Form_Decorator_Validator extends Zend_Form_Decorator_Abstract
{
    protected $_formValidators = array();
    
    /**
     * Render a form
     *
     * Replaces $content entirely from currently set element.
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $form = $this->getElement();        
        $this->_setUpValidators($form);
        
        $json = Zend_Json::encode($this->_formValidators);
        $id   = $form->getAttrib('id');

        if (!$id) {
            $id = uniqid();
            $form->setAttrib('id', $id);
        }
        
        return $content . "<script>var validator = new Validator('$id', '$json');</script>";
    }
    
    protected function _setUpValidators(La_Form $form, $validators = array(), $parentFormName = null, $return = false)
    {
        $subForms = $form->getSubForms();
        $formName = (!$parentFormName) ? $form->getName() : $parentFormName . '-' . $form->getName();
        
        if ($return) {
            $this->_formValidators = $validators;
            return;
        }

        foreach ($form->getValidators() as $key => $value) {
            $keyName = (isset($formName)) ? $formName . '-' . $key : $key;
            $validators[$keyName] = $value;
        }
        
        if ($subForms) {
            foreach($subForms as $subForm) {
                $this->_setUpValidators($subForm, $validators, $formName);
            }
            
        } else {
            $this->_setUpValidators($form, $validators, $formName, true);
        }       
    }
}