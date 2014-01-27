<?php

class La_Db_Table extends Zend_Db_Table
{   
    /**
    * table columns comment
    *
    * @var array
    */
    protected $_comments = array();
    
    /**
    * table validators
    *
    * example
    * 	array('id' => array( 'Int' , 'NotEmpty' ));
    *
    * @var array
    */
    protected $_validators = array();

    /**
    * table filters
    *
    * example
    * 	array('id' => array( 'Int' ));
    * 
    * @var array
    */
    protected $_filters = array();

    /**
    * Define se empresa_id vai ser adicionado automaticamente na query
    * 
    * @var bool
    */
    protected $_autoRestrict = false;
    
    /**
     * Armazena os valores das constraints dos domínios
     * 
     * @var bool
     */
    protected $_domainValues = array();
    
    /**
     * Define se a tabela ira fazer join automatico com as tabelas relacionadas
     * 
     * @var bool
     */
    protected $_autoJoin = false;
    
    /**
     * @return string
     */
    public function getName() 
    {
        return $this->_name;
    }
    
    /**
     * @return bool
     */
    public function getAutoRestrict() 
    {
        return $this->_autoRestrict;
    }
    
    /**
     * @return string
     */
    public function setAutoRestrict($autoRestrict)
    {
        return $this->_autoRestrict = $autoRestrict;
    }
    
    /**
    *
    * @return array 
    */
    public function getComments()
    {
        $cache = null;
        
        if (Zend_Registry::isRegistered('cache') && !$this->_comments) {
            $cache = Zend_Registry::get('cache');
            $key = $this->_name . "comments";
            $this->_comments = $cache->load($key);
        }
        
        if (!$this->_comments) {
            $this->_comments = $this->getDefaultAdapter()->getComments($this->_name);
            
            if ($cache) {
                $cache->save($this->_comments, $key);
            }
        }
        
        return $this->_comments;
    }
    
    public function getForHeaders(array $include = array(), array $exclude = array())
    {
        $comments = $this->getComments();
        $headers = array();
        
        if (count($include)) {
            foreach($include as $key => $value) {
                foreach ($comments as $key => $comment) {
                    if (in_array($key, $include)) {    
                        if($value == $key) {
                            $headers[$key] = $comment;
                            continue;
                        }
                    }
                }
                
                if (!isset($headers[$value])) {
                    $valueComment = Zend_Filter::filterStatic($value, 'Word_UnderscoreToSeparator');
                    $headers[$value] = ucfirst($valueComment);
                }
            }
        }
        
        if (count($exclude)) {
            $headers = $comments;
            foreach ($exclude as $value) {
                unset($headers[$value]);
            }
        }
        
        if (!$headers) {
            return $comments;
        }

        return $headers;
    }
    
    public function getColumns()
    {
        return $this->_getCols();
    }
    
    /**
    * Save data information
    *  
    * @param array $data
    * @return int 
    */
    public function save($data)
    {
        return $this->_saveCascade($data);
    }
    
    /**
     * Execute save data information
     * 
     * @param array $data
     * @param La_Db_Table $table
     * @return int
     */
    protected function _doSave(array $data, La_Db_Table $table = null)
    {
        if (!isset($table)) {
            $table = $this;
        }
        
        $data = array_intersect_key($data, array_flip($table->getColumns()));
        
        if (isset($data['id']) && $data['id']) {
            $table->update($data, array('id = ?' => $data['id']));
            return $data['id'];
        }
        
        unset($data['id']);
        $table->insert($data);
        
        return $table->getAdapter()->lastInsertId($table->getName(), 'id');
    }
    
    /**
     * Save relational data
     * 
     * @param array $data
     * @param string $table
     * @return int
     */
    protected function _saveCascade(array $data, $table = null)
    {
        $finalData  = false;
        $table      = (!isset($table)) ? $this->_name : $table;
        
        foreach($data as $key => $value) {
            if (!is_array($value)) {
                $finalData[$key] = $value;
            } else {
                $finalData[$key . '_id'] = $this->_saveCascade($value, $key);
            }
        }
        $lastInsertId = (int) $this->_doSave($finalData, new La_Db_Table($table));
        return $lastInsertId;
    }
    
    /**
    * Get all validators
    *
    * @return array
    */
    public function getValidators()
    {
        if (!$this->_validators) {
            $this->setupValidators();
        }
        return $this->_validators;
    }

    /**
    * Get all filters
    *
    * @return array
    */
    public function getFilters()
    {
        if (!$this->_filters) {
            $this->setupFilters();
        }
        return $this->_filters;
    }
    
    /**
    * Create validator's array
    *
    * @return void
    */
    public function setupValidators()
    {
        $this->_validators = array();
        $resultValidators  = $this->info();

        foreach ($resultValidators["metadata"] as $key => $value) {
            $this->_validators[$key] = array();
            
            if (strstr($value['DATA_TYPE'], 'enum') || $value['PRIMARY']) {
                continue;
            }
            
            if ($key == "created" || $key == "updated" || $key == "deleted") {
                continue;
            }
            
            if (!$value['NULLABLE']) {
                $this->_validators[$key][] = 'NotEmpty';
            }
            
            switch ($value['DATA_TYPE']) {
                case 'bpchar':
                case 'varchar':
                case 'char':
                case 'text':
                    if ($value['LENGTH'] > -1) {
                        $this->_validators[$key][] = array('StringLength', false, array(0, $value['LENGTH']));
                    }
                    break;
                case 'int':
                case 'int8':
                case 'int4':
                case 'int2':
                case 'bigint':
                case 'tinyint':
                case 'smallint':
                case 'bigint':
                case 'integer':
                    $this->_validators[$key][] = 'Digits';
                    break;
                case 'numeric':
                case 'decimal':
                case 'double':
                case 'float':
                case 'float8':
                    //$this->_validators[$key][] = 'Float';
                    break;
                case 'timestamp':
                case 'date':
                case 'datetime':
                case 'blob':
                case 'bytea':
                case 'time':
                case 'enum':
                    break;
                default:
                    if ($this->isDomain($key, $value['DATA_TYPE'])) {
                        $this->_validators[$key][] = array('InArray', false, array('haystack' => $this->_domainValues[$key]));
                    }
                    //throw new Exception('Tipo de dado não existe! ' . $value['DATA_TYPE']);
                break;
            }
        }
    }

    public function isDomain($field, $type) 
    {
        $cache = null;
        $key = sha1($type . "_domain_values");
        
        if (Zend_Registry::isRegistered('cache') 
            && !(isset($this->_domainValues[$field]) && $this->_domainValues[$field])) {
            $cache = Zend_Registry::get('cache');
            $this->_domainValues[$field] = $cache->load($key);
        }
        
        if (!(isset($this->_domainValues[$field]) && $this->_domainValues[$field])) {
            preg_match('/enum\((.*?)\)/', $type, $matches);
            $types = explode("','", trim($matches[1], "'"));

            $this->_domainValues[$field] = array_combine($types, $types);
            
            if ($cache) {
                $cache->save($this->_domainValues[$field], $key);
            }
            
            return true;
        }
        
        return true;
    }
    
    public function getDomainValues($field)
    {
        if (isset($this->_domainValues[$field])) {
            return $this->_domainValues[$field];
        }
        
        return array();
    }
    
    /**
    * Create filter's array
    *
    * @return void
    */
    public function setupFilters()
    {
        $this->_filters = array();
        $resultFilters  = $this->info();

        foreach ($resultFilters["metadata"] as $key => $value) {
            $this->_filters[$key] = array();
            
            if (strstr($value['DATA_TYPE'], 'enum')) {
                continue;
            }
            
            switch ($value['DATA_TYPE']) {
                case 'bpchar':
                case 'varchar':
                case 'char':
                case 'text':
                    $this->_filters[$key][] = 'StringTrim';
                break;
                case 'int':
                case 'int8':
                case 'int4':
                case 'int2':
                case 'bigint':
                case 'smallint':
                case 'tinyint':
                case 'bigint':
                case 'integer':
                    $this->_filters[$key][] = 'Int';
                break;
                case 'numeric':
                case 'decimal':
                case 'double':
                case 'float':
                case 'float8':
                    $this->_filters[$key][] = 'NormalizedToLocalized';
                break;
                case 'timestamp':
                case 'date':
                case 'datetime':
                    $this->_filters[$key][] = 'Date';
                break;
                case 'time':
                case 'blob':
                case 'bytea':
                case 'enum':
                    break;
            }

            if (strstr($value['COLUMN_NAME'], 'cnpj')
                || strstr($value['COLUMN_NAME'], 'cpf')) 
            {
                $this->_filters[$key][] = 'Digits';
            }
			
            $this->_filters[$key][] = 'Null';
        }
    }
    
    /**
    * Add validation to validator's array
    *
    * @return void
    */
    public function addValidators($field, $validators)
    {
        if (empty($this->_validators)) {
            $this->setupValidators();
        }

        $validators = (array) $validators;

        foreach($validators as $value) {
            $this->_validators[$field][] = $value;
        }
    }

    /**
    * Add filter to filter's array
    *
    * @return void
    */
    public function addFilters($field, $filters)
    {
        if (empty($this->_filters)) {
            $this->setupFilters();
        }

        $filters = (array) $filters;

        foreach($filters as $value) {
            $this->_filters[$field][] = $value;
        }
    }

    /**
    * Remove validation from validator's array
    *
    * @return void
    */
    public function removeValidators($field, $validators)
    {
        if (empty($this->_validators)) {
            $this->setupValidators();
        }

        $position = array_search($validators, $this->_validators[$field]);
        
        if (is_numeric($position)) {
            unset($this->_validators[$field][$position]);
        }
    }

    /**
    * Remove filter from filter's array
    *
    * @return void
    */
    public function removeFilters($field, $filters)
    {
        if (empty($this->_filters)) {
            $this->setupFilters();
        }

        $position = array_search($validators, $this->_validators[$field]);
        
        if (is_numeric($position)) {
            unset($this->_filters[$field][$position]);
        }
    }

    /**
    * @param  string                            $key
    * @param  string                            $value
    * @param  string|array|Zend_Db_Table_Select $where
    * @param  string|array                      $order
    * @param  integer                           $count
    * @param  integer                           $offset
    * @return array
    */
    public function fetchPairs($key = 'id', $value = 'name', $where = null, $order = null, $count = null, $offset = null)
    {
        $data = array();
        
        if (is_null($value)) {
            $value = $key;
        }
        
        if (is_null($order)) {
            $order = $value;
        }

        $rowset = $this->fetchAll($where, $order, $count, $offset);
        
        foreach ($rowset as $row) {
            if ($row[$key] && $row[$value]) {
                $data[$row[$key]] = $row[$value];
            }
        }
        
        return $data;
    }
    
    /**
    *
    * @param Zend_Db_Table_Select $select
    * @return type 
    */
    protected function _fetch(Zend_Db_Table_Select $select)
    {
        if (in_array('deleted', $this->_getCols())) {
            $select->where($this->_name . '.deleted = 0');
        }
        
        /*
        if ($this->getAutoRestrict()) {
            if (in_array($this->getAutoRestrict(), $this->_getCols())) {
                $identity = Zend_Auth::getInstance()->getIdentity();
                if (isset($identity->empresa_id) && $identity->empresa_id) {
                    $select->where($this->_name . '.' . $this->getAutoRestrict() .' = ?', $identity->empresa_id);
                }
            }
        }
        */
        
        if ($this->getAutoJoin()) {
            $select = $this->addAutoJoin($select);
        }
        
        return parent::_fetch($select);
    }
    
    public function logicDelete($where)
    {
        return $this->update(array('deleted' => 1), $where);
    }
    
    public function getColumnComment($columnName)
    {
        $comments = $this->getComments();
        
        if (isset($comments[$columnName])) {
            return $comments[$columnName];
        }
        
        return $columnName;
    }
    
    public function prepareWhere(array $values, $select = null, $alias = null) 
    {
        $where = array();
        
        if (!$alias) {
            $alias = $this->getName();
        }
        
        foreach ($values as $key => $value) {
            if ($value) {
                $key = $this->getDefaultAdapter()->quoteIdentifier($key);
                
                if (is_numeric($value)) {
                    $condition = "$alias.$key = ?";
                    $where[$condition] = $value;
                } elseif (is_string($value)) {
                    $condition = "$alias.$key LIKE ?";
                    $where[$condition] = "%$value%";
                }
            }
        }
        
        if ($select) {
            return $this->_where($select, $where);
        }
        
        return $where;
    }
    
    /**
     * @return bool
     */
    public function getAutoJoin() 
    {
        return $this->_autoJoin;
    }
    
    /**
     * @param bool
     */
    public function setAutoJoin($autoJoin)
    {
        $this->_autoJoin = $autoJoin;
    }
	
    /**
    * Create select with passed join tables 
    *
    * @param array $tables 
    * @param string|array $columns 
    * @param Zend_Db_Table_Select $select
    * @return Zend_Db_Table_Select
    */
    public function joinWith(array $tables, $columns = "*", Zend_Db_Table_Select $select = null)
    {
        if (!$select) {
            $select = $this->select(true);
        }
        
        $select->setIntegrityCheck(false)
               ->columns($columns);
        
        $references = $this->getDefaultAdapter()->getReferences(null, $this->_name);
        
        foreach ($tables as $alias => $table) {
            $column = $table . '_id';
            
            foreach ($references as $reference) {
                if ($reference['table'] == $table) {
                    $column = $reference['columns'];
                    break;
                }
            }
            
            $joinTable = array($alias => $table);
            $condition = sprintf('%s.id = %s.%s', $alias, $this->_name, $column);
            
            $select->joinLeft($joinTable, $condition, array());
        }
        
        return $select;
        
    }
    
    public function getNameForOptionField() 
    {
        $info = $this->info();
        $columnName = false;
        
        foreach ($info["metadata"] as $key => $value) {
            if ($this->getColumnComment($key) == 'Nome' || $this->getColumnComment($key) == 'Título') {
                $columnName = $key;
            }
        }
      
        if (!$columnName) {
            $possibleColumnTypes = array('varchar', 'text', 'char');
            foreach ($info["metadata"] as $key => $value) {
                if(in_array($value['DATA_TYPE'], $possibleColumnTypes)) { 
                    $columnName = $key;
                    break;
                }
            }
        }

        return $columnName;
    }
    
    protected function addAutoJoin(Zend_Db_Table_Select $select)
    {
        $tables     = array();
        $columns    = array();
        $references = $this->getDefaultAdapter()->getReferences(null, $this->_name);

        if ($references) {
            foreach ($references as $key => $reference) {
                $tableName   = $reference['table'];
                $table       = new La_Db_Table($tableName);
                $columnName  = $table->getNameForOptionField();
                $comments    = $this->getComments();
                $column      = $reference['columns'];
                $columnAlias = $comments[$column];
                $tableAlias  = $tableName . $key;
                
                $columns[$columnAlias] = $tableAlias . '.' . $columnName;
                $tables[$tableName]    = $tableName;
            
                $joinTable = array($tableAlias => $tableName);
                $condition = sprintf('`%s`.`id` = `%s`.`%s`', $tableAlias, $this->_name, $column);
                
                $select->joinLeft($joinTable, $condition, array());
            }
            
            $select->setIntegrityCheck(false)
                   ->columns($columns);
        }
        
        return $select;
    }
    
    public static function getHashPassword($password)
    {
        $sql = "SELECT crypt(?, gen_salt('bf'))";
        return self::getDefaultAdapter()->query($sql, array($password))->fetchColumn();
    }
}