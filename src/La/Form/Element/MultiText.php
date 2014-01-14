<?php
require_once 'Zend/Form/Element/Multi.php';

class La_Form_Element_MultiText extends Zend_Form_Element_Multi
{
    /**
     * Use formMultiCheckbox view helper by default
     * @var string
     */
    public $helper = 'formMultiText';

    /**
     * MultiCheckbox is an array of values by default
     * @var bool
     */
    protected $_isArray = true;
}
