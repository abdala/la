<?php

class La_View_Helper_Button 
{
    protected $_attrs = array();
    protected $_icon;
    protected $_name;
    
    /**
     * Create a link button
     * 
     * @param string $name
     * @param string $href
     * @param string $className
     * @param string $icon
     * @return string 
     */
    public function button(array $attrs = array())
    {
        $this->_attrs = $attrs;        
        return $this;
    }
    
    public function setName($name)
    {
    	$this->_name = $name;
    	return $this;
    }
    
    public function setIcon($icon) 
    {
    	$this->_icon = $icon;
    	return $this;
    }
    
    public function add($href)
    {
        $this->_name = 'Novo registro'; 
        $this->_icon = 'fa fa-book';
        $this->_attrs['class'] = (isset($this->_attrs['class']))? $this->_attrs['class'] . ' btn btn-primary' : 'btn btn-primary';
        $this->_attrs['href'] = $href;
        
        return $this;
    }
    
    public function back($href)
    {
        $this->_name = 'Voltar'; 
        $this->_icon = 'fa fa-reply fa-only';
        $this->_attrs['class'] = (isset($this->_attrs['class']))? $this->_attrs['class'] . ' btn btn-danger' : 'btn btn-danger';
        $this->_attrs['href'] = $href;
        
        return $this;
    }

    public function delete($href)
    {
        $this->_name = 'Excluir selecionados'; 
        $this->_icon = 'fa fa-ban-circle';
        $this->_attrs['class'] = (isset($this->_attrs['class']))? $this->_attrs['class'] . ' btn btn-danger delete_all' : 'btn btn-danger delete_all';
        $this->_attrs['href'] = $href;
        
        return $this;
    }
    
    public function __toString()
    {
    	$attrs = '';
    	foreach($this->_attrs as $key => $value) {
    		
    		$attrs .= $key . '="' . $value . '" ';
    	}
		
    	$html = '<a %s><i class="%s"></i> %s</a>';
        
        return sprintf($html, rtrim($attrs), $this->_icon, $this->_name);
    }
}
