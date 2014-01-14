<?php
class La_View_Helper_DynamicFormat extends Zend_View_Helper_Abstract
{   
    public function dynamicFormat($value)
    {
        if (is_numeric($value)) {
            if (strpos($value, ".") !== false) {
                return number_format($value, 2, ',', '.');
            }
            
            if (strlen($value) > 10) {
                return $this->view->formater(array())->cnpj($value);
            }
        } else {
            $dateFormats = array('Y-m-d' => 'd/m/Y', 'Y-m-d H:i:s' => 'd/m/Y H:i');
            $format      = get_date_format($value, $dateFormats);
            
            if ($format) {
                return date($format, strtotime($value));
            }
        }
        
        return $this->view->escape($value);
    }
}