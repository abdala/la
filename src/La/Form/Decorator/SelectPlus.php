<?php
class La_Form_Decorator_SelectPlus extends Zend_Form_Decorator_Abstract
{
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
        
        return $content . sprintf('<script>$("#%s").selectPlus("%s")</script>', $id, $this->getOption('url'));
    }
}