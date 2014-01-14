<?php
class La_View_Helper_FormSelectTable extends Zend_View_Helper_FormSelect
{
    public function formSelectTable($name, $value = null, $attribs = null,
        $options = null, $listsep = "<br />\n")
    {
        $script = '';
        
        if (isset($attribs['select-plus']) && $attribs['select-plus']) {
            $script = sprintf('<script>$("#%s").selectPlus("%s")</script>', $name, $attribs['select-plus']);
            unset($attribs['select-plus']);
        }
        
        $xhtml  = $this->formSelect($name, $value, $attribs, $options, $listsep);
        $xhtml .= $script;
        
        return $xhtml;
    }
}