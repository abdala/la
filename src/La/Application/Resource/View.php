<?php

/**
 * {@inheritdoc}
 */
class La_Application_Resource_View extends \Zend_Application_Resource_View
{
    /**
     * {@inheritdoc}
     *
     * @return Zend_View
     */
    public function getView()
    {
        if (null === $this->_view) {
            $options = $this->getOptions();
            $this->_view = new Zend_View($options);
            $this->_view->addHelperPath('La/View/Helper', 'La_View_Helper');
            $this->_view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
            
            if(isset($options['doctype'])) {
                $this->_view->doctype()->setDoctype(strtoupper($options['doctype']));
            }
        }
        return $this->_view;
    }
}