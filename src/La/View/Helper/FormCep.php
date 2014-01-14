<?php

class La_View_Helper_FormCep extends Zend_View_Helper_FormText
{

    public function formCep($name, $value = null, $attribs = null)
    {
        $id   = isset($attribs['id']) ? $attribs['id'] : $name;
        $html = $this->formText($name, $value, $attribs);

        $html .= "<script>";
        $html .= "(function($){
                    $('#$id').mask('99999-999');
                  })(jQuery);";
        $html .= "</script>";

        return $html;
    }

}
