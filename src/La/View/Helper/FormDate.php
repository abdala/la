<?php

class La_View_Helper_FormDate extends ZendX_JQuery_View_Helper_DatePicker
{

    public function formDate($name, $value = null, array $params = array(), array $attribs = array())
    {
        $id    = isset($attribs['id']) ? $attribs['id'] : $name;
        $html  = '<div class="input-group date">';
        $html .= '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
        $html .= $this->datePicker($name, $value, $params, $attribs);
        $html .= "</div>";
        
        $html .= "<script>";
        $html .= "(function($){
                    $('#$id').mask('99/99/9999');
                  })(jQuery);";
        $html .= "</script>";

        return $html;
    }

}
