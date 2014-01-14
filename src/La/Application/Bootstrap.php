<?php

class La_Application_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{   
    /**
     * Adds ZendX\Application53 library to plugin prefix path
     *
     * @param ZendX\Application53\Application $application
     */
    public function __construct($application)
    {
        $this->getPluginLoader()->addPrefixPath('La_Application_Resource','La/Application/Resource');
        parent::__construct($application);
    }
    
    public function _initCache()
    {
        $this->bootstrap('cachemanager');
        $options = $this->getOption('resources');
        
        if (!$options['cachemanager']['default']['active']) {
            return;
        }
        
        $cache = $this->getPluginResource('cachemanager')
                      ->getCacheManager()
                      ->getCache('default');
        
        $classFileIncCache = APPLICATION_PATH . '/../data/cache/pluginLoaderCache.php';
        if (file_exists($classFileIncCache)) {
            include_once $classFileIncCache;
        }
        
        Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        Zend_Registry::set('cache', $cache);
    }
    
    protected function _initZFDebug() 
    {
        if ($this->hasOption('zfdebug')) {
            $options = $this->getOption('zfdebug');
            
            if ($options['active']) {
                Zend_Loader_Autoloader::getInstance()->registerNamespace('ZFDebug');

                $this->bootstrap('FrontController');

                $front = $this->getResource('FrontController');

                if ($this->hasPluginResource('db')) {
                    $this->bootstrap('db');
                    $db = $this->getPluginResource('db')->getDbAdapter();
                    $options['plugins']['Database']['adapter'] = $db;
                }

                if ($this->hasPluginResource('cachemanager')) {
                    $this->bootstrap('cachemanager');
                    $cache  = $this->getPluginResource('cachemanager')
                           ->getCacheManager()
                           ->getCache('default');
                    $options['plugins']['Cache']['backend'] = $cache->getBackend();
                }
                
                $zfdebug = new ZFDebug_Controller_Plugin_Debug($options);
                $front->registerPlugin($zfdebug);
            }
        }
    }
}

