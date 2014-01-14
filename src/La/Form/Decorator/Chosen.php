<?php
class La_Form_Decorator_Chosen extends Zend_Form_Decorator_Abstract
{
    /**
     * Add javascript chosen call
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
        
        return $content . '<script>
                                $("#' . $id . '").chosen({
                                    no_results_text: "Não encontrado: ", 
                                    placeholder_text_multiple: "[selecione]"
                                });
                            </script>';
    }
}