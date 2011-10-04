<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Bootstrap class file.
 * The routines of the bootstrapping are here.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Bootstrap {
	public static $config;
	public static $view;
	public static $front;
	
	public function run() {
		self::prepare();
		
		try {
		    self::$front->dispatch();
		} catch(Exception $e) {
		    echo nl2br($e->__toString());
		}
	}
	
	public function prepare() {
		self::setupEnvironment();
		self::setupConfiguration();
		
		self::setupSession();
		self::setupDatabase();
		self::setupSmartyView();
		self::setupLayout();
		self::setupFrontController();
		self::setupLogger();
	}
	
	public function setupEnvironment() {
		
	}
	
	public function setupConfiguration() {
		self::$config = new Zend_Config_Xml(APP_PATH . '/config/config.xml', ENV);
		Zend_Registry::set('config',self::$config);
	}
	
	public function setupSession() {
		//init session
		$session = new Zend_Session_Namespace(self::$config->session_name);
		Zend_Registry::set('session',$session);
	}
	
	public function setupDatabase() {
		$db = Zend_Db::factory(Zend_Registry::get('config')->database);
		Zend_Db_Table::setDefaultAdapter($db);
		Zend_Registry::set('db', $db);
	}
	
	public function setupSmartyView() {
		/**
		 * Init the Smarty view wrapper and set smarty suffix to the view scripts.
		 */
		self::$view = new EZ_View_Smarty(self::$config->smarty->toArray());
		
		//use the viewrenderer to keep the code DRY
		//instantiate and add the helper in one go
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView(self::$view);
		$viewRenderer->setViewSuffix(self::$config->smarty->suffix);
	}
	
	public function setupLayout() {
		/**
		 * Set inflector for Zend_Layout
		 */
		$inflector = new Zend_Filter_Inflector(':script.:suffix');
		$inflector->addRules(array(':script' => array('Word_CamelCaseToDash', 'StringToLower'),
					 								  'suffix'  => self::$config->layout->suffix));
		
		// Initialise Zend_Layout's MVC helpers
		Zend_Layout::startMvc(array('layoutPath' => APP_PATH.'/chamista'.self::$config->layout->layoutPath,
									'view' => self::$view,
									'contentKey' => self::$config->layout->contentKey,
									'inflector' => $inflector));
	}
	
	public function setupFrontController() {
		self::$front = Zend_Controller_Front::getInstance();
		self::$front->throwExceptions(true);
		self::$front->setControllerDirectory(array(
		    'default' => APP_PATH.'/chamista/controllers'
		));
		
		$errorHandler = new Zend_Controller_Plugin_ErrorHandler();
		$errorHandler->setErrorHandler(array('controller' => 'error', 'action' => 'application', 'module' => 'chamista'));
		
		self::$front->registerPlugin( $errorHandler );
		self::$front->throwExceptions( false );
		
		$router = self::$front->getRouter();
		$route = new Rewaz_Controller_Router_Route(
			':controller/:action/*',
			array(
				'controller' => 'index',
				'action'     => 'index'
			)
		);
		$router->addRoute('default', $route);
	}
	
	public function setupLogger() {
		//enable logging to default.log
		$writer = new Zend_Log_Writer_Stream(APP_PATH.'/data/log/default.log');
		$logger = new Zend_Log($writer);
		 
		//give easy access to the logger
		Zend_Registry::set('logger',$logger);
	}
}