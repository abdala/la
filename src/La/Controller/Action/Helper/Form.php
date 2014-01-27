<?php

class La_Controller_Action_Helper_Form
    extends Zend_Controller_Action_Helper_Abstract 
{
    /**
     * 
     * @param La_Db_Table $table
     * @param La_Form $form 
     */
    public function direct(La_Db_Table $table = null, La_Form $form = null)
    {
        $controller  = $this->getActionController();
        $id          = Zend_Filter::filterStatic($controller->getParam("id"), 'int');
        $ajaxContext = $controller->getHelper('AjaxContext');
        $formData    = $controller->getParam("formData");
        
        $ajaxContext->addActionContext('form', 'html')
                    ->initContext();
        
        if (!$form) {
            $form = $controller->form;
        }
        
        if (!$table) {
            $table = $controller->table;
        }
        
        $table->setAutoJoin(false);
        $controller->view->data = $table->createRow();
        
        if ($id) {
            $row = $table->find($id)->current();
            if (!$row) {
                $controller->view->messages = array($controller->getHelper('Message')->direct('ERROR_LOAD'));
            } else {
                $controller->view->data = $row;
                $form->populate($row->toArray());
                
                if ($formData) {
                    $form->populate($formData);
                    $controller->view->messages = array($controller->getHelper('Message')->direct('ERROR', true));
                }
            }
        }
        
        $controller->view->dynamicFormId = uniqid();
        $controller->view->form = $form;
        $controller->view->id = $id;
    }
}