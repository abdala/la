<?php

class La_View_Helper_Messages extends Zend_View_Helper_Abstract
{
    
    public function messages()
    {
        $html = '';
        $icon = array('success' => 'check', 'warning' => 'fire', 'error' => 'bolt');
        if ($this->view->messages) {
            $html .= '<div class="messages">';
            foreach ($this->view->messages as $message) {
                $html .= sprintf('<div class="alert alert-%s">', key($message));
                if(isset($icon[key($message)])) {
                    $html .= sprintf('<i class="fa fa-%s"></i>&nbsp;', $icon[key($message)]);
                }
                $html .= current($message);
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        return $html;
    }
}