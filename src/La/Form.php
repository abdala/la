<?php

class La_Form extends ZendX_JQuery_Form
{
    /**
     * validators
     *
     * @var array
     */
    protected $_validators = array();
    
    /**
     * table elements
     *
     * @var array
     */
    protected $_tableElements = array();
    
    /**
     * Lista dos elementos que serão carregados automaticamente
     *
     * @var string|array
     */
    protected $_fields = '*';
    
    /**
     *
     * @var \La_Db_Table 
     */
    protected $_table;
    
    /**
     *
     * @param array|Zend_Config $options 
     */
    public function __construct($options = null) 
    {        
        $this->addPrefixPath('La_Form_Decorator', 'La/Form/Decorator', 'decorator')
             ->addPrefixPath('La_Form_Element', 'La/Form/Element', 'element')
             ->addElementPrefixPath('La_Form_Decorator', 'La/Form/Decorator', 'decorator')
             ->addDisplayGroupPrefixPath('La_Form_Decorator', 'La/Form/Decorator')
             ->addPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator', 'decorator')
             ->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element')
             ->addElementPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator', 'decorator')
             ->addDisplayGroupPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator');
        
        $this->addElementPrefixPath('La_Filter', 'La/Filter', 'FILTER');
        $this->addElementPrefixPath('La_Validate', 'La/Validate', 'VALIDATE');
        $this->addElementPrefixPath('Zebra_Validate', 'Zebra/Validate', 'VALIDATE');
        
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
        
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'div', 'class' => 'zend_form')),
            'Form',
            'Validator'
        ));
        
        if ($this->_table) {
            $this->_setDefaultElements();
        }
        
        parent::__construct($options);
        
        if ($this->_table) {
            $this->setFilters($this->_table->getFilters());
            $this->setValidators($this->_table->getValidators());
        }
    }
    
    /**
     *
     * @return \La_Db_Table 
     */
    public function getTable()
    {
        return $this->_table;
    }
    
    /**
     *
     * @param La_Db_Table $table 
     */
    public function setTable(La_Db_Table $table)
    {
        $this->_table = $table;
    }
    
    /**
     *
     * @param string|array $elements 
     */
    public function setFields($fields)
    {
    	if(!in_array('id', $fields))
    		array_unshift($fields, 'id');
    	
        $this->_fields = $fields;
    }
    
    /**
     *
     * @return string|array $elements 
     */
    public function getFields()
    {
        return $this->_fields;
    }
    
    /**
     *
     * @param array $filters
     * @return \La_Form 
     */
    public function setFilters(array $filters) 
    {
        foreach ($filters as $name => $filter) {
            $element = $this->getElement($name);
            if ($element) {
                $element->addFilters($filter);
            }
        }
        return $this;
    }
    
    /**
     *
     * @param array $validators
     * @return \La_Form 
     */
    public function setValidators(array $validators) 
    {
        $this->_validators = $validators;
        
        foreach ($validators as $name => $validator) {
            $element = $this->getElement($name);
            if ($element) {
                if (! $element instanceof Zend_Form_Element_File) {
                    $element->addValidators($validator);
                }
            }
        }
        
        return $this;
    }
    
    /**
     *
     * @return array
     */
    public function getValidators()
    {
        return $this->_validators;
    }
    
    public function removeValidator($elementName, $validatorName)
    {
        $validatorName = (array)$validatorName;
        
        foreach ($validatorName as $validator) {
            if (isset($this->_validators[$elementName])) {
                $validators = $this->_validators[$elementName];
            
                foreach ($validators as $key => $value) {
                    $name = is_array($value) ? $value[0] : $value;
                    if ($name == $validator) {
                        unset($this->_validators[$elementName][$key]);
                    }
                }
            }
        }
    }
    
    
    /**
     *
     * @param array $elements 
     */
    protected function _setDefaultElements() 
    {
        $elements = $this->_getDefaultElements();

        foreach ($elements as $name => $element) {
            if (is_string($element)) {
                $element = array($element, null);
            }
            
            parent::addElement($element[0], $name, $element[1]);
            
            $this->getElement($name)->removeDecorator('HtmlTag');
            $this->getElement($name)->getDecorator('Label')->removeOption('tag');
            $this->getElement($name)->addDecorator(array('wrapper' => 'HtmlTag'),   
                                                   array('tag' => 'div', 'class' => 'form-group col-sm-3'));
        }
        
        $id = $this->getElement('id');
        
        if ($id) {
            $id->setDecorators(array('ViewHelper'));
        }
        
        parent::addElement('button', 'Enviar', array('class' => 'btn btn-small btn-primary', 'type' => 'submit'));
        $this->getElement('Enviar')->removeDecorator('DtDdWrapper');
        $this->getElement('Enviar')->addDecorator(array('wrapper' => 'HtmlTag'),   
                                                  array('tag' => 'div', 'class' => 'submit'))
                                   ->setOrder(1000);
    }

    protected function _changeFilters()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            $filters = $element->getFilters();
            foreach ($filters as $filter) {
                if ($filter instanceof Zend_Filter_NormalizedToLocalized) {
                   $element->removeFilter(get_class($filter));
                   $element->addFilter('LocalizedToNormalized');
                }
            }
        }
    }


    public function isValid($data) 
    {
        $this->_changeFilters();
        return parent::isValid($data);
    }
    
    public function isValidPartial(array $data) 
    {
        $this->_changeFilters();
        return parent::isValidPartial($data);
    }
    
    /**
     *
     * @return \La_Form 
     */
    public function removeRequired()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->setRequired(false);
        }
        
        return $this;
    }
    
    public function removeElements(array $include = array(), array $exclude = array())
    {
        foreach ($this->getElements() as $element) {
            if (count($include) && !in_array($element->getName(), $include)) {
                $this->removeElement($element->getName());
            }

            if (count($exclude) && in_array($element->getName(), $exclude)) {
                $this->removeElement($element->getName());
            }
        }
    }
    
    protected function _getForeignTable($references, $field) {
        foreach ($references as $value) {
            if ($value['columns'] == $field) {
                return $value['table'];
            }
        }
        
        return false;
    }
    
    /**
     * Create elements array
     *
     * @return void
     */
    protected function _setupElements()
    {
        $info            = $this->_table->info();
        $references      = $this->_table->getDefaultAdapter()->getReferences(null, $this->_table->getName());
        $dynamicElements = $this->getFields();
        
        foreach ($this->_orderMetada($info["metadata"], $dynamicElements) as $key => $value) {
            $isDefaultTypes = true;
            if ($dynamicElements == '*' || in_array($key, (array) $dynamicElements)) {
                if ($key == "created" || $key == "updated" || $key == "deleted") {
                    continue;
                }
                
                $foreignTableName = $this->_getForeignTable($references, $key);
            
                $options = array('label' => $this->_table->getColumnComment($key),
                                 'class' => 'form-control');
            
                if ($value['LENGTH'] > -1) {
                    $options['maxlength'] = $value['LENGTH'];
                }

                if (!$value['NULLABLE'] && !$value['PRIMARY']) {
                    $options['required'] = true;
                }
            
                switch ($value['DATA_TYPE']) {
                    case 'bpchar':
                    case 'varchar':
                    case 'char':
                        $this->_tableElements[$key] = array('text', $options);
                        break;
                    case 'text':
                        $options['rows'] = '8';
                        //$options['cols'] = '15';
                        //$options['class'] = 'input-xxlarge';
                        $this->_tableElements[$key] = array('textarea', $options);
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
                        $options['class'] .= ' digits';
                        $this->_tableElements[$key] = array('digits', $options);
                        break;
                    case 'numeric':
                    case 'decimal':
                        $options['class'] .= ' number';
                        $this->_tableElements[$key] = array('number', $options);
                        break;
                    case 'timestamp':
                    case 'date':
                    case 'datetime':
                        $this->_tableElements[$key] = array('date', $options);
                        break;
                    default:
                        $isDefaultTypes = false;
                        $this->_tableElements[$key] = array('text', $options);
                        break;
                }

                if ($value['PRIMARY']) {
                    $this->_tableElements[$key] = 'hidden';
                    continue;
                }
            
                if (strstr($value['COLUMN_NAME'], 'cnpj')) {
                    $options['class'] .= ' cnpj';
                    $options['filters'] = array('Digits');
                    $options['validators'] = array('Cnpj');
                    $options['maxlength'] = 18;
                    $this->_tableElements[$key]  = array('Cnpj', $options);
                    continue;
                }
            
                if (strstr($value['COLUMN_NAME'], 'cpf')) {
                    $options['class'] .= ' cpf';
                    $options['filters'] = array('Digits');
                    $options['validators'] = array('Cpf');
                    $options['maxlength'] = 14;
                    $this->_tableElements[$key]  = array('Cpf', $options);
                    continue;
                }
                
                if (strstr($value['COLUMN_NAME'], 'phone')) {
                    $options['class'] .= ' telefone';
                    $options['filters'] = array('Digits');
                    $options['maxlength'] = 16;
                    $this->_tableElements[$key]  = array('Telefone', $options);
                    continue;
                }
            
                if (strstr($value['COLUMN_NAME'], 'cep')) {
                    $options['class'] .= ' cep';
                    $options['filters'] = array('Digits');
                    $options['validators'] = array('Cep');
                    $options['maxlength'] = 9;
                    $this->_tableElements[$key]  = array('Cep', $options);
                    continue;
                }
            
                if ($value['COLUMN_NAME'] == 'password') {
                    $options['class'] .= ' password';
                    $options['validators'] = array('PasswordConfirmation');
                    $this->_tableElements[$key] = array('Password', $options);
                
                    unset($options['validators']);
                    $options['label'] = 'Confirmação de senha';
                    $this->_tableElements['password_confirmacao'] = array('Password', $options);
                    continue;
                }
            
                if ($foreignTableName) {
                    $foreignTable = new La_Db_Table($foreignTableName);
                    $valueColumn  = $foreignTable->getNameForOptionField();
                
                    $options['tableName']   = $foreignTableName;
                    $options['keyColumn']   = 'id';
                    $options['valueColumn'] = $valueColumn;

                    $this->_tableElements[$key] = array('TableSelect', $options);
                    continue;
                }
                
                if (!$isDefaultTypes) {
                    if ($this->_table->isDomain($key, $value['DATA_TYPE'])) {
                        $domainValues = $this->_table->getDomainValues($key);
                        
                        if ($domainValues) {
                            $options['multioptions'] = array('' => '[selecione]') + $domainValues;
                            $options['validators'] = array(array('InArray', false, array('haystack' => $domainValues)));
                            $this->_tableElements[$key] = array('Select', $options);
                        }
                    }
                }
            }
        }
    }
    
    /**
    * Orders metadata according to the order indicated
    * 
    * @param array $metadata
    * @param array $order
    * 
    * @return array
    */
    protected function _orderMetada($metadata, $fields) 
    {
        $out = array();
        
        if (!is_array($fields)) {
            $out = $metadata;
        } else {
            foreach ($fields as $order => $field) {
                if (isset($metadata[$field])) {
                    $out[$field] = $metadata[$field];
                }
            }
            
            foreach ($metadata as $item) {
                if(!isset($out[$item['COLUMN_NAME']])) {
                    $out[$item['COLUMN_NAME']] = $item;
                }
            }
        }
        
        return $out;
    }
    
    /**
     * Get all table elements
     *
     * @return array
     */
    protected function _getDefaultElements()
    {
        if (!$this->_tableElements) {
            $this->_setupElements();
        }
        return $this->_tableElements;
    }
    
    
    public function elementsForFilter()
    {
        $action = str_replace("save", "index", $this->getAction());
        $this->setAction($action)
             ->setMethod('get');
        
        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->setRequired(false);
            if ($element->getAttrib('select-plus')) {
                $element->setAttrib('select-plus', false);
            }
        }
        
        $this->removeDecorator('Validator');
        $this->getElement('Enviar')->setName('Pesquisar');
        
        return $this;
    }
    
    public function __get($name)
    {
        if (isset($this->_elements[$name])) {
            return $this->_elements[$name];
        } elseif (isset($this->_subForms[$name])) {
            return $this->_subForms[$name];
        } elseif (isset($this->_displayGroups[$name])) {
            return $this->_displayGroups[$name];
        }

        return $this->getElement($name);
    }
    
    public function getElement($name)
    {
        $element = parent::getElement($name);
        
        if (!$element) {
            $name = $name ? $name : uniqid();
            $element = new La_Form_Element_Mock($name);
        }
        
        return $element;
    }
    
    public function addElement($element, $name = null, $options = null)
    {
        if (!$name) {
            $name = $element->getName();
            if (!$name) throw new Exception('Defina um nome para o campo');
        }
        
        parent::addElement($element, $name, $options);
        $this->decorateElement($this->getElement($name));
        
        return $this;
    }
    
    /**
     *
     * @param Zend_Form_Element|string $element
     * @return Zend_Form_Element 
     */
    public function replaceElement($element) 
    {
        $name  = $element->getName();
        $previousElement = $this->getElement($name);
        
        if ($previousElement) {
            $label = $this->getElement($name)->getLabel();
            $element->setLabel($label);
        }
        
        $this->addElement($element);
        
        return $this;
    }
    
    public function replaceFileElement($name)
    {
        $this->replaceElement(new Zend_Form_Element_File($name));
        $decoratos = $this->getElement($name)->getDecorators();
        
        array_splice($decoratos, 0, 0, 'InputFile');
        
        $this->getElement($name)->setDecorators($decoratos);
    }
    
    public function decorateElement(Zend_Form_Element $element)
    {
        
        if (! $element instanceof Zend_Form_Element_File) {
            $element->addDecorator('ViewHelper');
        }
        
        $element->addDecorator('HtmlTag', array('tag' => 'div'));
        $element->addDecorator('Label', array('tag' => null));
        $element->addDecorator(array('wrapper' => 'HtmlTag'),   
                               array('tag' => 'div', 'class' => 'form-group col-sm-3'));
                               
        return $element;
    }
    
    public function getValues($suppressArrayNotation = false)
    {
        $return = parent::getValues($suppressArrayNotation);
        
        foreach ($return as $element => $value) {
            if ($this->$element instanceof Zend_Form_Element_File && !$value) {
                unset($return[$element]);
            }
        }
        
        return $return;
    }
}
