<?php

class La_Controller_Action_Helper_List
    extends Zend_Controller_Action_Helper_Abstract 
{
    /**
     * 
	 * @param array $headers
     * @param Zend_Db_Table_Select $select
	 * @param La_Form $form
     * @return \La_Paginator 
     */
    public function direct(array $headers = array(), Zend_Db_Table_Select $select = null, Zend_Db_Table_Abstract $table = null, La_Form $form = null)
    {
        $controller = $this->getActionController();
        $data       = $this->getRequest()->getParams();
        
        if (!$table) {
            $table = $controller->table;
        }
        
        if (!$select) {
            $select = $table->select();
        }
        
        if (!$form) {
            if (isset($controller->form)) {
                $form = $controller->form;
            }
        }
        
        if ($headers) {
            if (is_numeric(key($headers))) {
                $headers = $table->getForHeaders($headers);
            }
        }
        
        if ($form) {
            $form->removeRequired()
                 ->elementsForFilter();
            
            if ($form->isValidPartial($data)) {
                $select = $table->prepareWhere($form->getValues(), $select);
            }
            
            $form->populate($form->getValues());
            $controller->view->form = $form;
        }
        
        if (isset($data['column']) && isset($data['direction'])) {
            $controller->view->order = array('column' => $data['column'], 'direction' => $data['direction']);
            $select->order(array($data['column'] . " " . $data['direction']));
        } else {
            $select->order(array('id DESC'));
            $controller->view->order = array('column' => 'id', 'direction' => 'DESC');
        }
        
        $controller->view->headers = $headers;
        $controller->view->data    = $controller->getHelper('paginator')->direct($select);
    }
}