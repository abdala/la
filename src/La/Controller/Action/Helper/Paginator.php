<?php

class La_Controller_Action_Helper_Paginator 
    extends Zend_Controller_Action_Helper_Abstract 
{
    /**
     * 
     * @param Zend_Db_Select|Zend_Db_Table_Select $select
     * @return \La_Paginator 
     */
    public function direct(Zend_Db_Select $select)
    {
        $request   = $this->getRequest();
        $page      = Zend_Filter::filterStatic($request->getParam('page', 1), 'int');
        $quantity  = Zend_Filter::filterStatic($request->getParam('quantity', 10), 'int');
        $paginator = new La_Paginator($select, $page, $quantity);
        
        return $paginator->getResult();
    }
}