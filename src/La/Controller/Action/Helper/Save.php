<?php

class La_Controller_Action_Helper_Save
    extends Zend_Controller_Action_Helper_Abstract 
{
    protected $_url = '%s/%s/form/id/%s/table/%s';
    
    protected $_responseXmlHttpRequest = false;
    
    /**
     * Seta uma url para fazer o redirecionamento
     * @param string $url 
     * @return object
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }
    
    /**
     * Seta uma função anonima para deixar a resposta do ajax dinâmica.
     * A função anonima recebe um array com o id (ultimo elemento salvo na tabela),
     * e os elementos do form.
     * 
     * @return object
     */
    public function setResponseXmlHttpRequest(callable $function)
    {
        $this->_responseXmlHttpRequest = $function;
        
        return $this;
    }
    
    /**
     * 
     * @param Zend_Db_Select|Zend_Db_Table_Select $select
     * @return \La_Paginator 
     */
    public function direct(La_Db_Table $table = null, La_Form $form = null, $url = null)
    {
        $controller  = $this->getActionController();
        $request     = $this->getRequest();
        $data        = $request->getParams();
        $ajaxContext = $controller->getHelper('AjaxContext');
        
        $ajaxContext->addActionContext('save', 'json')
                    ->initContext();
        
        $form = $form ?: $controller->form;
        $table = $table ?: $controller->table;
        
        if ($request->isPost() && $form->isValid($data)) {
            $formData = $form->getValues();
            $id = $table->save($formData);
            
            if (!$url) {
                $url = sprintf($this->_url, $data['module'], $data['controller'], $id, $table->getName());
            }
            
            if ($this->getRequest()->isXmlHttpRequest()) {
                $jsonData = array(
                    'id' => $id,
                    'name' => $formData[$table->getNameForOptionField()]
                );
                
                if ($this->_responseXmlHttpRequest) {
                    $formData['id'] = $id;
                    $function = $this->_responseXmlHttpRequest;
                    $jsonData = $function($formData);
                }
                
                $controller->getHelper('json')->direct($jsonData);
                return;
            }
             
            $message = $controller->getHelper('Message')->direct('SUCCESS');
            $controller->redirect($url);
        }
        
        $params = array('formData' => $form->getValues());
        
        $controller->forward('form', null, null, $params);
    }
}