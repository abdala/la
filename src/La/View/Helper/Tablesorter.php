<?php

class La_View_Helper_Tablesorter extends La_View_Helper_SimpleTable
{
    protected $_order = array('column' => 'id', 'direction' => 'ASC');
    
    public function setOrder(array $order)
    {
        $this->_order = $order;
        return $this;
    }
    
    /**
     *
     * @param array $headers
     * @param array|Zend_Db_Table_Rowset $data
     * @param array $buttons
     * 
     * @return \La_View_Helper_SimpleTable 
     */
    public function tablesorter($headers = array(), $data = array(), $buttons = array())
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
                $html .= sprintf('<td width="20"><label><input type="checkbox" id="check-%s"><span class="lbl"></span></label></td>', $this->_id);
            }
            
            if ($buttonsLength) {
                $html .= sprintf('<td width="%d">&nbsp;</td>', ($buttonsLength * 30));
            }
            
            foreach ($this->_headers as $key => $header) {
                $orderBy = null;
                if ($this->_order['column'] == $key) {
                    $orderBy = $this->_order['direction'] == 'ASC' ? 'headerSortUp': 'headerSortDown';
                }
                
                $width = null;
                if ($key == 'id') {
                    $width = 'width="40"';
                } 
                
                $html .= sprintf('<th %s class="header %s">%s<input type="hidden" value="%s"></th>', $width, $orderBy, $header, $key);
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
                            $td = '<td>%s</td>';
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
                        
                        $(".list .header").unbind("click").click(function(e){
                            var $children = $(this).find("input"),
                                direction = "ASC",
                                urlParts = window.location.toString().split("?"), queryString = "",
                                queryObject = {column: null, direction: null};

                            if( this.className.indexOf("headerSortUp") > 1 ) {
                                direction = "DESC";
                            }
            
                            if (urlParts.length > 1) {
                                queryStringParts = urlParts[1].split("&");
                                $.each(queryStringParts, function(i){
                                    keyValue = queryStringParts[i].split("=");
                                    queryObject[keyValue[0]] = keyValue[1];
                                });
                            }
            
                            queryObject.column    = $children.eq(0).val();
                            queryObject.direction = direction;
                            
                            $.each(queryObject, function(key){
                                if (key) {
                                    queryString += key + "=" + queryObject[key] + "&";
                                }
                            });
            
                            window.location = urlParts[0] + "?" + queryString;
                        });
                    });
                  </script>';
        } catch ( Exception $e ) {
            return $e->getMessage();
        }
        return $html;
    }
}