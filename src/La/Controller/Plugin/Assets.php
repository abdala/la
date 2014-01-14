<?php

class La_Controller_Plugin_Assets extends Zend_Controller_Plugin_Abstract
{
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		if (APPLICATION_ENV !== 'production') {
			
			$DS 				 = DIRECTORY_SEPARATOR;
			$standardPath 		 = APPLICATION_PATH . $DS . 'modules' . $DS . '%s' . $DS . 'assets' . $DS . '%s' . $DS;
			$finalModulePath 	 = APPLICATION_ROOT . $DS . 'public' . $DS . 'assets' . $DS . 'modules' . $DS . '%s' . $DS . '%s' . $DS;
			$viewPath            = 'assets' . $DS . 'modules' . $DS . '%s' . $DS . '%s' . $DS . '%s';
			
			$modulePaths['js']  = sprintf($standardPath, $request->getModuleName(), 'js');
			$modulePaths['css'] = sprintf($standardPath, $request->getModuleName(), 'css');
			$modulePaths['img'] = sprintf($standardPath, $request->getModuleName(), 'img');
			
			$files = array();
			$view  = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
			
			foreach($modulePaths as $type => $path) {
				
				if (@opendir($path)) {
					
					$finalPath        = sprintf($finalModulePath, $request->getModuleName(), $type);
					$directoryItertor = new DirectoryIterator($path);
					
					if (!@opendir($finalPath)) {
						mkdir($finalPath, 0775, true);
					}
					
					foreach($directoryItertor as $file) {
						
						if ($file->isFile()) {
							
							$info = $file->getFileInfo();
							if (!in_array($info->getPathname(), $files)) {
								
								copy($file->getPathname(), ($finalPath . $file->getFilename()));
								
								if (file_exists($finalPath . $file->getFilename()) && $type !== 'img') {
	
									$appendFile   = sprintf($viewPath, $request->getModuleName(), $type, $file->getFilename());
									$viewMethod   = ($type === 'js') ? 'headScript' : 'headLink';
									$appendMethod = ($type === 'js') ? 'appendFile' : 'appendStylesheet';
									
									$view->$viewMethod()->$appendMethod($appendFile);
								}
							}
						}
					}
				} 
			}
		}
	}
}