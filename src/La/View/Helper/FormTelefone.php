<?php

class La_View_Helper_FormTelefone extends Zend_View_Helper_FormText 
{
    public function formTelefone($name, $value = null, $attribs = null)
    {
        $id   = isset($attribs['id']) ? $attribs['id'] : $name;
        $html = $this->formText($name, $value, $attribs);
        
        /*
        $html .= "<script>";
        $html .= "(function($){
                    $('#$id').mask('(99) 9999-9999?9', {placeholder: ' '});
                  })(jQuery);";
        $html .= "</script>";
        */
        return $html;
    }
}
