<?php

class La_Filter_Datetime implements Zend_Filter_Interface
{
    /**
     * Set options
     * @var array
     */
    protected $_options = array(
        'locale'      => null,
        'date_format' => null,
        'precision'   => null
    );

    /**
     * Class constructor
     *
     * @param array|Zend_Config $options (Optional)
     */
    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (null === $options) {
            $locale = Zend_Registry::get('Zend_Locale')->toString();
            $dateFormat = Zend_Locale_Data::getContent($locale, 'datetime');

            $options = array('locale' => $locale, 'date_format' => $dateFormat);
        }
        
        $this->setOptions($options);
    }

    /**
     * Returns the set options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets options to use
     *
     * @param  array $options (Optional) Options to use
     * @return Tri_Filter_Datetime
     */
    public function setOptions(array $options = null)
    {
        $this->_options = $options + $this->_options;
        return $this;
    }

    /**
     * Filter datetime
     *
     * @param string $value
     * @return null|string
     */
    public function filter($value)
    {
        if ($value) {
            $date = new Zend_Date($value, null, $this->_options['locale']);
            if (Zend_Date::isDate($value, $this->_options['date_format'], $this->_options['locale'])) {
                return $date->toString('yyyy-MM-dd HH:mm:ss');
            } else {
                return $date->toString($this->_options['date_format']);
            }
        }
        return null;
    }
}
