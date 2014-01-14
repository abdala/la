<?php

class La_Controller_Action_Helper_Download extends Zend_Controller_Action_Helper_Abstract 
{
    public function direct($filepath, $filename = null)
    {
        if (file_exists($filepath)) {
            $controller = $this->getActionController();
            $controller->getHelper('layout')->disableLayout();
            $controller->getHelper('viewRenderer')->setNoRender();
        
            if (!$filename) {
                $filename = basename($filepath);
            }
            
            $etag = md5_file($filepath);
            $size = filesize($filepath);
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Etag: ' . $etag);
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . $size);
            
            ob_clean();
            flush();
            
            readfile($filepath);
        }
    }
}