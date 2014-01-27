<?php

class La_View_Helper_SimpleTable extends Zend_View_Helper_Abstract
{
    /**
     *
     * @var int 
     */
    protected $_id;

    /**
     *
     * @var array 
     */
    protected $_headers = array();

    /**
     *
     * @var array|Zend_Db_Table_Rowset 
     */
    protected $_data = array();

    /**
     *
     * @var array
     */
    protected $_buttons = array();

    /**
     *
     * @var array
     */
    protected $_formaters = array();

    /**
     *
     * @var int
     */
    protected $_parentId;

    /**
     *
     * @var type boolean
     */
    protected $_activePaginator = true;

    /**
     *
     * @var type boolean
     */
    protected $_showCheckbox = true;
    
    /**
     *
     * @var type boolean
     */
    protected $_lineLink = '<a href="%s/%s/form/id/%s/table/%s" class="table-edit">%s</a>';

    /**
     *
     * @var type string
     */
    protected $_module;

    /**
     *
     * @var type string
     */
    protected $_controller;
    
    /**
     *
     * @var type string
     */
    protected $_action;
    
    /**
     *
     * @var type string
     */
    protected $_deleteUrl;

    /**
     *
     * @var type array
     */
    protected $_totalizers;

    /**
     *
     * @param array $headers
     * @param array|Zend_Db_Table_Rowset $data
     * @param array $buttons
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function simpleTable($headers = array(), $data = array(), $buttons = array())
    {
        $this->_id = uniqid();
        
        if (!$headers) {
            $headers = $this->view->headers;
        }
        
        if (!$data) {
            $data = $this->view->data;
        }

        $this->setHeaders($headers);
        $this->setData($data);
        $this->setButtons($buttons);

        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * 
     * @param array $headers
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;
        return $this;
    }

    /**
     * 
     * @param string $module
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    /**
     * 
     * @param string $controller
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * 
     * @param string $header
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function addHeader($header)
    {
        $this->_headers[] = $header;
        return $this;
    }

    /**
     * 
     * @param array $formaters
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function setFormaters(array $formaters)
    {
        $this->_formaters = $formaters;
        return $this;
    }

    /**
     * 
     * @param string $key
     * @param string $formater
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function addFormater($key, $formater)
    {
        $this->_formaters[$key] = $formater;
        return $this;
    }

    /**
     *
     * @param type $data
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     *
     * @param array $buttons
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function setButtons(array $buttons)
    {
        $this->_buttons = $buttons;
        return $this;
    }

    /**
     *
     * @return int 
     */
    public function getParentId()
    {
        return $this->_parentId;
    }

    /**
     *
     * @param int $parentId
     * @return \La_View_Helper_SimpleTable
     */
    public function setParentId($parentId)
    {
        $this->_parentId = $parentId;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getActivePaginator()
    {
        return $this->_activePaginator;
    }

    /**
     * @param $activePaginator boolean
     * @return \La_View_Helper_SimpleTable
     */
    public function setActivePaginator($activePaginator)
    {
        $this->_activePaginator = $activePaginator;
        return $this;
    }
    
    /**
     *
     * @return boolean
     */
    public function getLineLink()
    {
        return $this->_lineLink;
    }

    /**
     * @param $lineLink boolean
     * @return \La_View_Helper_SimpleTable
     */
    public function setLineLink($lineLink)
    {
        $this->_lineLink = $lineLink;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->_deleteUrl;
    }

    /**
     * @param $$deleteUrl string
     * @return \La_View_Helper_SimpleTable
     */
    public function setDeleteUrl($deleteUrl)
    {
        $this->_deleteUrl = $deleteUrl;
        return $this;
    }
    
    /**
     * @param $showCheckbox boolean
     * @return \La_View_Helper_SimpleTable
     */
    public function setShowCheckbox($showCheckbox)
    {
        $this->_showCheckbox = $showCheckbox;
        return $this;
    }

    public function getTotalizers()
    {
        return $this->_totalizers;
    }

    public function setTotalizers($totalizers)
    {
        $this->_totalizers = $totalizers;
        return $this;
    }

    /**
     *
     * @param string $button
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function addButton($button)
    {
        $this->_buttons[] = $button;
        return $this;
    }

    public function __toString()
    {
        try {
            $request       = Zend_Controller_Front::getInstance()->getRequest();
            $buttonsLength = count($this->_buttons);
            $module        = $this->_module;
            $controller    = $this->_controller;
            
            if (!$module) {
                $module = $request->getModuleName();
            }
            
            if (!$controller) {
                $controller = $request->getControllerName();
            }
            
            $url = sprintf("%s/%s/delete/parent_id/%s/table/%s", $module, $controller, $this->getParentId(), $this->view->table);
            if ($this->_deleteUrl) {
                $url = $this->_deleteUrl;
            }

            $html  = '<div class="list">';
            $html .= sprintf('<form action="%s" method="post" id="%s">', $url, $this->_id);
            $html .= '<table class="table table-striped table-bordered table-hover">';
            $html .= '<thead>';
            $html .= '<tr>';
            
            if ($this->_showCheckbox) {
                $html .= sprintf('<td width="20"><input type="checkbox" id="check-%s"></td>', $this->_id);
            }
            
            if ($buttonsLength) {
                $html .= sprintf('<td width="%d">&nbsp;</td>', ($buttonsLength * 30));
            }
            
            foreach ($this->_headers as $key => $header) {
                if ($key == 'id') {
                    $html .= sprintf('<th width="40">%s</th>', $header);
                } else {
                    $html .= sprintf('<th>%s</th>', $header);
                }
            }

            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            $formater = $this->view->formater($this->_formaters);

            if (count($this->_data)) {
                foreach ($this->_data as $key => $data) {
                    $html .= '<tr>';
                
                    if ($this->_showCheckbox) {
                        $html .= sprintf('<td><label><input class="check-delete" type="checkbox" name="id[]" value="%s"><span class="lbl"></span></label></td>', $data['id']);
                    }
                
                    if ($buttonsLength) {
                        $html .= '<td>';

                        foreach ($this->_buttons as $button) {
                            $html .= sprintf($button, $data['id']);
                        }

                        $html .= "</td>";
                    }
                
                    foreach ($this->_headers as $field => $header) {
                        $formaterHelper = $formater->getHelperByKey($field);
                        if ($this->getLineLink()) {
                            $td = sprintf('<td>' . $this->getLineLink() . '</td>', $module, $controller, $data['id'], $this->view->table, '%s');
                        } else {
                            $td = "<td>%s</td>";
                        }
                    
                        if ($formater->getHelperByKey($field)) {
                            $html .= sprintf($td, $formater->$formaterHelper($field, $data));
                        } else {
                            $html .= sprintf($td, $this->view->dynamicFormat($data[$field]));
                        }
                    }

                    $html .= "</tr>";
                }

                if ($this->getTotalizers() != null) {
                    $totalizers   = $this->getTotalizers();
                    $countHeaders = count($this->_headers);
                
                    if ($this->_showCheckbox) {
                        $countHeaders++;
                    }
                
                    if ($buttonsLength) {
                        $countHeaders++;
                    }
                
                    $colspan = ($countHeaders - count($totalizers));
                    $html .= '<tr>';
                    $html .= sprintf('<td colspan="%d"></td>', $colspan);
                
                    foreach ($totalizers as $i => $totalizer) {
                        $html .= sprintf('<td><strong id="%s">%s</strong></td>', $i, $totalizer); 
                    }
                
                    $html .= '</tr>';
                }
            } else {
                $html .= sprintf('<td colspan="%d">Nenhum registro encontrado.</td>', (count($this->_headers)+1));
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</form>';
            $html .= '</div>';
            
            if ($this->getActivePaginator()
                && $this->_data instanceof Zend_Paginator
                && $this->_data->getTotalItemCount() > 10)
            {            
                $html .= $this->view->paginationControl($this->_data);
            }
            
            $html .= '<script>
                    $(document).ready(function(){
                        $(".delete_all").click(function(){
                            $checked = $("#' . $this->_id . '").find("input:checked");
                            
                            if ($checked.length == 0) {
                                bootbox.alert("Nenhum registro selecionado.");
                                return false;
                            }
                            
                            bootbox.confirm("Deseja realmente excluir os registros?", function($result) {
                                if($result) {
                                    $("#' . $this->_id . '").submit();
                                }
                            });
                            return false;
                        });

                        $("#check-' . $this->_id . '").click(function(){
                            var checked = $(this).prop("checked") ? true :  false;
                            $(this).parents("table").find("input.check-delete")
                                                    .prop("checked", checked);
                        });
                    });
                  </script>';
        } catch ( Exception $e ) {
            return $e->getMessage();
        }
        return $html;
    }
}