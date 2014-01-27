<?php
class La_Form_Decorator_Cleditor extends Zend_Form_Decorator_Abstract
{
    protected $_options = array('controls' => 'bold italic underline');
    /**
     * Add javascript selectplus call
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
        
        return $content . sprintf('
            <script>
                var editor_%1$s = $("#%1$s").cleditor({controls: "%2$s"});
                editor_%1$s.bind("focused", function(){
                    $(".cleditorMain").find(".errors").hide("slow");
                });
            </script>', 
        $id, $this->getOption('controls'));
    }
}