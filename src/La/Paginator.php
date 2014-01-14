<?php

class La_Paginator extends Zend_Paginator
{
    /**
     * Query will be paged.
     *
     * @var Zend_Db_Select or Zend_Db_Table_Select
     */
    protected $_select;

    /**
     * Page current.
     *
     * @var integer
     */
    protected $_page;

    /**
     * Items per page.
     *
     * @var integer
     */
    protected $_quantity;

    /**
     * (non-PHPdoc)
     * @see Zend_Paginator#__construct()
     */
    public function __construct($select, $page, $quantity = 10)
    {
        $this->_select   = $select;
        $this->_page     = $page;
        $this->_quantity = $quantity;
    }

    /**
     * @return Zend_Paginator
     */
    public function getResult()
    {
        $paginator = Zend_Paginator::factory($this->_select);
        $paginator->setCurrentPageNumber($this->_page);
        $paginator->setItemCountPerPage($this->_quantity);
        $paginator->setDefaultScrollingStyle('Sliding');

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        return $paginator;
    }
}
