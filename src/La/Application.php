<?php
require_once('Zend/Application.php');

/**
 * {@inheritdoc}
 */
class La_Application extends Zend_Application
{
    protected $_cacheEnabled = false;
    /**
     * {@inheritdoc}
     *
     * Overridden to add ZendX and La namespace to autoloader
     *
     * @param string $environment
     * @param array $options
     */
    public function __construct($environment, $options = null)
    {
        require_once 'Zend/Loader/Autoloader.php';
        Zend_Loader_Autoloader::getInstance()->registerNamespace('La')
                                             ->registerNamespace('Zebra')
                                             ->registerNamespace('ZendX');
        parent::__construct($environment, $options);
    }

    /**
     * Get bootstrap object
     *
     * @return Zend_Application_Bootstrap_BootstrapAbstract
     */
    public function getBootstrap()
    {
        if (null === $this->_bootstrap) {
            $this->_bootstrap = new La_Application_Bootstrap($this);
        }
        return $this->_bootstrap;
    }
    
    protected function _loadConfig($file)
    {
        $filename = str_replace('ini', 'php', basename($file));
        $path     = APPLICATION_PATH . '/../data/cache/' . $filename;
        
        if (file_exists($path)) {
            return require($path);
        }
        
        $config      = parent::_loadConfig($file);
        
        if ((isset($config['resources']['cachemanager']['default']['active'])
            && $config['resources']['cachemanager']['default']['active'])
            || $this->_cacheEnabled) 
        {
            $this->_cacheEnabled = true;
            $arrayString = "<?php\n return " . var_export($config, true) . ";\n";
        
            file_put_contents($path, $arrayString);
        }
        
        return $config;
    }
}