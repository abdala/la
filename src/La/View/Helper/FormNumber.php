<?php

class La_View_Helper_FormNumber extends Zend_View_Helper_FormText 
{
    public function formNumber($name, $value = null, $attribs = null)
    {
        $id    = isset($attribs['id']) ? $attribs['id'] : $name;
        $html  = '<div class="input-group">';
        $html .= '<span class="input-group-addon"><i class="fa fa-usd"></i></span>';
        $html .= $this->formText($name, $value, $attribs);
        $html .= "</div>";
        
        $html .= "<script>";
        $html .= "(function($){
                    $('#$id').keyup(function(){
                        mascara(this, mvalor);
                    });
                  })(jQuery);";
        $html .= "</script>";
        
        return $html;
    }
}
