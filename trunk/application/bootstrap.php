<?php
/**
 * 
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	
	protected function _initAutoload(){
		$front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
                                'module'     => 'error',
                                'controller' => 'error',
                                'action'     => 'error'
        )));
		
        $autoloader = new Zend_Application_Module_Autoloader(array( 
                    'namespace' => '', 
                    'basePath' => APPLICATION_PATH, 
                )); 
        return $autoloader; 
    } 
	
	protected function _initLoadPlugin(){
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Vqt_Controller_Plugins_Auth());
        // $front->registerPlugin(new Vqt_Plugins_Tracking());
    }  
	
	protected function _initDatabase(){
        $dbOptionds = $this->getOption('resources');
        $dbOptionds = $dbOptionds['db'];
        
        // Set up db
        $db = Zend_Db::factory($dbOptionds['adapter'], $dbOptionds['params']);
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        
        Zend_Registry::set('connectDB', $db);
        // Khi thiet la che do nay model moi co the su dung duoc
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        // return it, so that it can be stored in bootstrap
        return $db;
    }
    
    protected function _initSetConstants(){
        $front = Zend_Controller_Front::getInstance();
        $front->addModuleDirectory(APPLICATION_PATH . "/modules");
		
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'landing_page');
		
        $router = new Zend_Controller_Router_Rewrite();
        $router->addConfig($config, 'routes');
        $front->setRouter($router);
        return $front;
    }
}
