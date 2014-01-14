<?php

class La_Form_Element_TableSelect extends Zend_Form_Element_Select
{
    /**
     * Use formSelectTable view helper by default
     * @var string
     */
    public $helper = 'formSelectTable';
    
    /**
     *
     * @var string 
     */
    protected $_tableName;
    
    /**
     *
     * @var string
     */
    protected $_keyColumn = 'id';
    
    /**
     *
     * @var string
     */
    protected $_valueColumn = 'nome';
    
    /**
     *
     * @var string|array|Zend_Db_Table_Select
     */
    protected $_where;

    /**
     *
     * @var array
     */
    protected $_initialOption = array('' => '[selecione]');

    public function init()
    {
        $options = $this->_initialOption;
        
        if ($this->getAttrib('multiple')) {
            $options = array();
        }
        
        $table = new La_Db_Table($this->_tableName);
        $options += $table->fetchPairs($this->_keyColumn, $this->_valueColumn, $this->_where);
        
        $this->setMultiOptions($options);
    }
    
    public function setInitialOption($initialOption)
    {
        $this->_initialOption = $initialOption;
    }
    
    public function getInitialOption()
    {
        return $this->_initialOption;
    }
    
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
    }
    
    public function setTableClass($tableClass)
    {
        $table = new $tableClass;
        $this->_tableName = $table->getName();
    }
    
    public function setKeyColumn($keyColumn)
    {
        $this->_keyColumn = $keyColumn;
    }
    
    public function setValueColumn($valueColumn)
    {
        $this->_valueColumn = $valueColumn;
    }
    
    public function setWhere($where)
    {
        $this->_where = $where;
    }
}
