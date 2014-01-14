<?php
class La_Form_Decorator_GoogleMaps extends Zend_Form_Decorator_Abstract
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
        
        return $content . '<div class="element">
                                <label>&nbsp;</label>
                                <a href="#' . $id . '" class="btn btn-maps">Abrir Google Maps</a>
                           </div>';
    }
}