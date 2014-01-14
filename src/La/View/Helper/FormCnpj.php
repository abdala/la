<?php

class La_View_Helper_FormCnpj extends Zend_View_Helper_FormText 
{
    public function formCnpj($name, $value = null, $attribs = null)
    {
        $id   = isset($attribs['id']) ? $attribs['id'] : $name;
        $html = $this->formText($name, $value, $attribs);
        
        $html .= "<script>";
        $html .= "(function($){
                    $('#$id').mask('99.999.999/9999-99');
                  })(jQuery);";
        $html .= "</script>";
        
        return $html;
    }
}
