<?php

class La_View_Helper_Stage extends Zend_View_Helper_Abstract
{
    protected $_options;
    protected $_request;
    protected $_direction;
    
    /**
     * Execute BootContainer helper
     * 
     * @param string $direction
     * @throws Exception
     */
    public function stage($content, $direction = 'left')
    { 
        $availableDirections = array('left', 'right', 'top', 'below');
        
        if (!in_array($direction, (array) $availableDirections)) {
            throw new Exception('Posição indisponível!');
        }
        
        $this->_direction   = $direction;
        $this->_request     = Zend_Controller_Front::getInstance()->getRequest();
        $this->_options     = $this->view->stageOptions;
        
        $class = 'tabbable';
        if ($this->_direction !== 'top') {
            $class .= ' tabs-' . $this->_direction;
        }
        
        $out = '<div class="' . $class . '">';
        $out .= $this->_renderTabs();
        $out .= $this->_renderPanes($content);
        $out .= '</div>';
        return $out;
    }
    
    /**
     * Render stage tabs
     */
    protected function _renderTabs()
    {
        $out = '<ul class="nav nav-tabs">';
        $id  = $this->_request->getParam('id');
        
        foreach ($this->_options['stages'] as $key => $stage) {
            $tabClass = ($key == $this->_options['stage']) ? 'active' : null;
            $href = sprintf(' href="workflow/%s/%d/id/%d"', $this->_options['name'], $key, $id);
                    
            if (!in_array($key, $this->_options['freeTabs'])) {
                $href = '';
            }
            
            $out .= '<li class="' . $tabClass . '"><a' . $href . '>' . $stage['label'] . '</a></li>';
        }
        
        $out .= '</ul>';
        
        return $out;
    }
    
    /**
     * Render stage content
     */
    protected function _renderPanes($content)
    {
        $out = '<div class="tab-content">';
        $out .= '<div class="tab-pane in active">';
        $out .= $content;
        $out .= '</div>';
        $out .= '</div>';
        
        return $out;
    }
}