<?php
function get_date_format($value, $formats)
{
    $formats = (array) $formats;
    
    foreach ($formats as $inputFormat => $outputFormat) {
        $date = date_create_from_format($inputFormat, $value);
        
        if ($date && date_format($date, $inputFormat) == $value) {
            return $outputFormat;
        }
    }
    
    return  false;
}