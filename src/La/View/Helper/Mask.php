<?php

class La_View_Helper_Mask 
{
    /**
     * Formats a value using the mask passed
     * 
     * Ex: mask(12345678, '####-####);
     * 
     * @param type $value
     * @param type $mask
     * @return type 
     */
    public function mask($value, $mask)
    {
        $output = Zend_Filter::filterStatic($value, 'Alnum');
        $index  = -1;
        $len    = strlen($mask);
        
        for ($i = 0; $i < $len; ++$i) {
            if ($mask[$i] == '#' && isset($output[++$index])) {
                $mask[$i] = $output[$index];
            }
        }

        return $mask;
    }
}
