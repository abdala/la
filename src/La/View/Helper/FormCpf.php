<?php

class La_View_Helper_FormCpf extends Zend_View_Helper_FormText 
{
    public function formCpf($name, $value = null, $attribs = null)
    {
        $id   = isset($attribs['id']) ? $attribs['id'] : $name;
        $html = $this->formText($name, $value, $attribs);
        
        $html .= "<script>";
        $html .= "(function($){
                    $('#$id').mask('999.999.999-99');
                  })(jQuery);";
        $html .= "</script>";
        return $html;
    }
}
