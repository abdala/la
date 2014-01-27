<?php
class La_Form_Decorator_InputFile extends Zend_Form_Decorator_Abstract
{
    /**
     *
     * Replaces $content entirely from currently set element.
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $el = $this->getElement();        
        $id = $el->getAttrib('id');

        if (!$id) {
            $id = uniqid();
            $el->setAttrib('id', $id);
        }
        
        $html = sprintf('<div class="controls">
                            <div class="input-group">
                                <input id="%s" class="form-control" type="text">
                                <span class="input-group-btn">
        						    <button id="%s" class="btn" type="button">Buscar</button>
        					    </span>
        				    </div>
        			    </div>', $id.'input', $id.'btn');
                        
        $html .= $content; 
        
        $html .= sprintf('<script> 
            $(function(){
                $("#%s").css({"position":"absolute","left":"-9999px"})
                          .attr("tabindex", "-1")
                          .change(function(){ 
                              var content = this.value.split("\\\").pop() + ", ";
                              
                              if (content !== "") {
                                  $("#%s").val(content.replace(/\, $/g, ""));
                              }
                          });
                          
                $("#%s").click(function(){ $("#%1$s").click(); });
            });
        </script>', $id, $id.'input', $id.'btn'); 
                
        return $html;
        // return $content;
    }
}