<?php

class La_Filter_UniqueName implements Zend_Filter_Interface
{
    /**
     * Rename uploaded file
     *
     * @param string $value
     * @return null|string
     */
    public function filter($value)
    {
        $basename  = basename($value);
        $parts     = explode('.', $basename);
        $extension = end($parts);
        
        $newName  = microtime(true) . '.' . $extension;
        $newValue = str_replace($basename, $newName, $value);
        
        rename($value, $newValue);
        
        return $newValue;
    }
}
