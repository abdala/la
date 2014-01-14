<?php

class La_View_Helper_FormDigits extends Zend_View_Helper_FormText 
{
    public function formDigits($name, $value = null, $attribs = null)
    {
        $html = $this->formText($name, $value, $attribs);
        
        $html .= "<script>";
        $html .= "(function($){
                    $('#$name').keyup(function(){
                        mascara(this, soNumeros);
                    });
                  })(jQuery);";
        $html .= "</script>";
        
        return $html;
    }
}
