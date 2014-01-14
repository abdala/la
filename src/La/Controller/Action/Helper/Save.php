<?php

class La_Controller_Action_Helper_Save
    extends Zend_Controller_Action_Helper_Abstract 
{
    /**
     * 
     * @param Zend_Db_Select|Zend_Db_Table_Select $select
     * @return \La_Paginator 
     */
    public function direct(La_Db_Table $table = null, La_Form $form = null, $viewScript = null)
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
            
            $url = sprintf('%s/%s/form/id/%s/table/%s', $data['module'], $data['controller'], $id, $table->getName());
            
            if ($this->getRequest()->isXmlHttpRequest()) {
                $jsonData = array(
                    'id' => $id,
                    'name' => $formData[$table->getNameForOptionField()]
                );
                $controller->getHelper('json')->direct($jsonData);
                return;
            }
             
            $message = $controller->getHelper('Message')->direct('SUCCESS');
            $controller->redirect($url);
        }
        
        $form->populate($form->getValues());
        
        $controller->view->messages = array($controller->getHelper('Message')->direct('ERROR', false));
        $controller->view->errors   = $form->getMessages();
        $controller->view->form     = $form;
        
        if ($viewScript) {
            $filter       = new Zend_Filter_Word_UnderscoreToDash();
            $scriptFolder = $filter->filter($controller->view->table);
            
            if (is_readable(sprintf($viewScript, $scriptFolder))) {
                $controller->renderScript(sprintf("%s/form.phtml", $scriptFolder));
                return;
            }
        }
        
        $controller->render('form');
    }
}