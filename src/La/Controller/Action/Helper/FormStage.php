<?php

class La_Controller_Action_Helper_FormStage 
    extends Zend_Controller_Action_Helper_Abstract
{
    protected $_id;
    protected $_name;
    protected $_options = array(
            'currentStage' => 0, 
            'freeTabs' => array(0 => 0)
    );
    protected $_controller;
    protected $_session;
    protected $_stages;
    protected $_finalURL;
    
    /**
     * Execute FormStage helper 
     * 
     * @param array $stages
     * @param string $saveFunction
     * @param string $name
     * @throws Exception
     */
    public function direct(array $stages, $name)
    {
        $this->_name                = $name;
        $this->_controller          = $this->getActionController();
        $this->_session             = new Zend_Session_Namespace('FORM_STAGE');
        $this->_options['stages']   = $stages;
        $this->_id                  = (int) $this->_controller->getParam('id');

        if (!count($stages)) {
            throw new Exception('Nenhum estágio associado!');
        }

        if (!$this->_controller->hasParam('stage')) {
            $this->_session->unsetAll();
        }

        if (isset($this->_session->_stages[$this->_name][$this->_id]['stages'])) {
            $this->_options = $this->_session->_stages[$this->_name][$this->_id];
        }
        
        if ($this->_controller->hasParam('stage')) {
            $this->_options['currentStage'] = $this->_controller->getParam('stage') - 1;
        }
        
        if (!$this->_checkJumpStages()) {
            
            if ($this->_controller->getRequest()->isPost()) {
                $this->setCurrentData($this->_controller->getRequest()->getParams());
                
                if ($this->_executePostCallback()) {
                    
                    if (!$this->_isComplete()) {
                        $this->_session->_stages[$this->_name][$this->_id] = $this->_options;
                        $this->_redirectToStage($this->_options['currentStage'] + 2);
                        return;
                    }
                    
                    unset($this->_session->_stages[$this->_name][$this->_id]);
                    $this->_controller->redirect($this->getFinalURL());
                    return;
                }
                
            } else {
                if (!$this->_executePreCallback()) {
                    
                    if ($this->getCurrentStage() !== 0) {
                        $this->_redirectToStage($this->getCurrentStage() - 1);
                    }
                }
            }
        }
        
        $currentStage                                       = $this->_options['currentStage'];
        $this->_options['freeTabs'][$currentStage]          = $currentStage;
        $this->_session->_stages[$this->_name][$this->_id]  = $this->_options;
        $this->_controller->view->stageOptions              = $this->_options;
    }
    
    public function setCurrentData($data) 
    {
        $this->_options['data'][$this->_options['currentStage']] = $data;
        return $this;
    }
    
    public function getCurrentData()
    {
        return $this->_options['data'][$this->_options['currentStage']];
    }
    
    public function getCurrentStage()
    {
        return $this->_options['currentStage'];
    }
    
    public function getMergedData()
    {
        $data = call_user_func_array('array_merge_recursive', $this->_options['data']);
        $data = array_map(function($item) {
            
            if (is_array($item)) {
                
                $item = array_map(function($subItem){
                    
                    if (is_array($subItem)) {
                        $subItem = array_unique($subItem);
                        
                        if (count($subItem) === 1) {
                            $subItem = reset($subItem);
                        }
                    }
                    
                    return $subItem;
                }, $item);
            }
            
            return $item;
        }, $data);
        
        return $data;
    }
    
    public function setFinalURL($url)
    {
        $this->_finalURL = $url;
        return $this;
    }
    
    public function getFinalURL()
    {
        if (!isset($this->_finalURL)) {
            $request = $this->_controller->getRequest();
            $this->_finalURL = sprintf('/%s/%s/%s/', 
                    $request->getModuleName(), $request->getControllerName(), $request->getActionName());
        }
        return $this->_finalURL;
    }
    
    public function setFreeTabs($tabs)
    {
        $this->_options['freeTabs'] = $tabs;
        return $this;
    }
    
    public function getFreeTabs()
    {
        return $this->_options['freeTabs'];
    }
    
    public function setStageData($data, $stage) 
    {
        if (!isset($this->_options['data'][$stage])) {
            $this->_options['data'][$stage] = $data;
        }
        
        return $this;
    }
    
    public function getStageData($stage)
    {
        if (isset($this->_options['data'][$stage])) {
            return $this->_options['data'][$stage];
        }
        
        return array();
    }
    
    /**
     * Check that all stages were completed
     * 
     * @return boolean
     */
    protected function _isComplete()
    {
        if (count($this->_options['stages']) 
                === ($this->_options['currentStage'] + 1)) {
            return true;   
        }
        
        return false;
    }
    
    /**
     * Check jump stages
     * 
     * @return boolean
     */
    protected function _checkJumpStages()
    {
        $currentStage = $this->_options['currentStage'];
        
        if (isset($this->_options['freeTabs'])
            && !in_array($currentStage - 1, $this->_options['freeTabs']) && $currentStage !== 0) 
        {
            $freeTabs = $this->_options['freeTabs'];
            $this->_redirectToStage($freeTabs[count($freeTabs) - 1] + 1);
            return true;
        }
        
        return false;
    }
    
    /**
     * Execute user post-callback function
     */
    protected function _executePostCallback()
    {
        if ($this->_hasPostCallback()) {
            $lastPostCallback = $this->_getPostCallback();   
                     
            if (!$this->_controller->$lastPostCallback($this)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check that has user post-callback function
     * 
     * @return boolean
     */
    protected function _hasPostCallback()
    {
        $currentStage = $this->_options['currentStage'];
        
        if (isset($this->_options['stages'][$currentStage]['postCallback'])
            && is_callable(array($this->_controller, $this->_options['stages'][$currentStage]['postCallback']))) 
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get user post-callback function
     */
    protected function _getPostCallback()
    {
        $currentStage = $this->_options['currentStage'];
        return $this->_options['stages'][$currentStage]['postCallback'];
    }
    
    /**
     * Execute user pre-callback function
     */
    protected function _executePreCallback()
    {
        if ($this->_hasPreCallback()) {
            $preCallback = $this->_getPreCallback();   
                     
            if (!$this->_controller->$preCallback($this)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check that has user pre-callback function
     * 
     * @return boolean
     */
    protected function _hasPreCallback()
    {
        $currentStage = $this->_options['currentStage'];
        if (isset($this->_options['stages'][$currentStage]['preCallback'])
            && is_callable(array($this->_controller, $this->_options['stages'][$currentStage]['preCallback']))) 
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get user pre-callback function
     */
    protected function _getPreCallback()
    {
        $currentStage = $this->_options['currentStage'];
        return $this->_options['stages'][$currentStage]['preCallback'];
    }
    
    /**
     * Redirect to stage $stage
     * 
     * @param integer $stage
     */
    protected function _redirectToStage($stage)
    {
        $id      = $this->_controller->getParam('id');
        $request = $this->_controller->getRequest();
        $url = sprintf('/%s/%s/%s/stage/%s/id/%s', $request->getModuleName(), 
                $request->getControllerName(), $request->getActionName(), $stage, $id);
        $this->_controller->redirect($url);
    }
}