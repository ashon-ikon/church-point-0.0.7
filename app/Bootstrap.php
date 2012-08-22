<?php
//Pneumonoultramicroscopicsilicovolcanoconiosis

// Site-wide define
require_once(APPLICATION_PATH . '/helper_functions.php');

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	var $view 			= null;
	var $config 		= null;
	var $extraConfig 	= null;
	
	/**
	 *  Initialize DB
	 *  //////////////////////
	 */
	public function _initDefaultDB()
	{	
		$config	 	= new Zend_Config($this->getOptions());
		
        $options	= $config->toArray();
		
		$this->bootstrap('db');
	
		$dbAdapter = $this->getResource('db');
	
		Zend_Db_Table::setDefaultAdapter($dbAdapter);
	
	}
	
	public function _initTimeZone()
	{
		/**
		 * -------------
		 * Extract config
		 */
		$config	 	= $this->getConfig();
		if ($config->app->time->zone)
			date_default_timezone_set($config->app->time->zone);
	}
	
	/**
	 * This function helps to keep the website accessbile from other reference
	 */
	public function _r_initSetBaseUrl()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$script_name =	$_SERVER['SCRIPT_NAME'];
		$lUri	 = strlen($uri);
		$niddle = null;
		//---Do some smart magic
		for($i = 0; $i < $lUri; $i++)
		{
			if ($script_name[$i] !== $uri[$i]) break;
		}
		$niddle = $i-1;
	
		$url_offset = substr($uri,0, $niddle);
		
		
		$controller = Zend_Controller_Front::getInstance();
		$controller->setControllerDirectory(APPLICATION_PATH . '/controllers')
		           ->setBaseUrl($url_offset); // set the base url!
	}
	
		protected function _initAppDefines()
	{
		/**
		 * -------------
		 * Extract config
		 */
		$config	 	= $this->getConfig();
		$options	= $config->app->media->toArray();
	
        defined('APP_MEDIA_DIRECTORY') ||
			define ('APP_MEDIA_DIRECTORY', $options['root'] ); 

        defined('APP_PUBLIC_DIRECTORY') ||
			define ('APP_PUBLIC_DIRECTORY', realpath(APPLICATION_PATH . DS. '..' . DS . 'public' . DS) . DS); 

		defined('APP_IMAGES_DIRECTORY') ||
			define ('APP_IMAGES_DIRECTORY', $options['images'] ); 

		defined('APP_IMAGES_THUMB_DIRECTORY') ||
			define ('APP_IMAGES_THUMB_DIRECTORY', $options['imagesthumb'] ); 

		defined('APP_IMAGES_THUMB_PREFIX') ||
			define ('APP_IMAGES_THUMB_PREFIX', $options['image']['thumbnail']['prefix'] ); 

		defined('APP_IMAGES_THUMB_WIDTH') ||
			define ('APP_IMAGES_THUMB_WIDTH', $options['image']['thumbnail']['width'] ); 

		defined('APP_IMAGES_THUMB_HEIGHT') ||
			define ('APP_IMAGES_THUMB_HEIGHT', $options['image']['thumbnail']['height'] ); 

		defined('APP_PROFILE_IMAGES_DIRECTORY') ||
			define ('APP_PROFILE_IMAGES_DIRECTORY', $options['profileimages'] ); 	
 		
		defined('APP_NO_PROFILE_IMAGE') ||
			define ('APP_NO_PROFILE_IMAGE', $options['noprofileimage'] ); 	
 		
 		defined('APP_EVENTS_IMAGES_DIRECTORY') ||
			define ('APP_EVENTS_IMAGES_DIRECTORY', $options['eventsimages'] ); 
 		
 		defined('APP_SLIDER_IMAGES_DIRECTORY') ||
			define ('APP_SLIDER_IMAGES_DIRECTORY', $options['sliderimages'] ); 
 		
 		defined('APP_SERMON_AUTHORS_IMAGES_DIRECTORY') ||
			define ('APP_SERMON_AUTHORS_IMAGES_DIRECTORY', $options['sermonauthorsimages'] ); 

		/* Helpers */
		defined('ONE_DAY') ||
			define('ONE_DAY',  60 * 60 * 24);
		
		defined('KILO_BYTE') ||
			define ('KILO_BYTE', pow(2 , 10 ));
			

		$other_options = $config->toArray();

		
		defined('APP_DOMAIN') ||
			define ('APP_DOMAIN',   'http://'.$other_options['app']['domain'] );
		
		defined('COOKIE_AGE') ||
			define ('COOKIE_AGE',  $other_options['app']['cookie']['duration'] );
			
		defined('APP_COOKIE_NAME') ||
			define ('APP_COOKIE_NAME',  $other_options['app']['cookie']['name'] );
			
		defined('APP_PAGE_REDIRECT') ||
			define ('APP_PAGE_REDIRECT',  $other_options['app']['pages']['redirect'] );
			
		defined('APP_SALT') ||
			define ('APP_SALT',  $other_options['app']['encryption']['salt']);
	
		defined('APP_CHURCH_NAME') ||
			define ('APP_CHURCH_NAME', $other_options['app']['churchname'] );
		
		defined('APP_CHURCH_DESCRIPTION') ||
			define ('APP_CHURCH_DESCRIPTION', $other_options['app']['churchdesc'] );
		
		defined('APP_CHURCH_EMAIL') ||
			define ('APP_CHURCH_EMAIL', $other_options['app']['churchemail'] );
		
		defined('APP_PAGE_TEMPLATES') ||
			define ('APP_PAGE_TEMPLATES', realpath($other_options['app']['pages']['templates'] ) . DS);
		
	}
	
	/**
	 *  Define the Custom Namespaces 
	 */
	public function _initAutoloader()
	{
	
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->suppressNotFoundWarnings(true);
		$autoloader->registerNamespace('Facebook_');
		//$autoloader->registerNamespace('RainTpl_');
		$autoloader->registerNamespace('Point_');
		
		$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
			'basePath'  => APPLICATION_PATH . '/usr',
			'namespace' => 'Point',
		));
		$resourceLoader->addResourceType('controller', 	'controllers/',		'Controller');
		$resourceLoader->addResourceType('config', 		'config/',			'Config');
		$resourceLoader->addResourceType('editor',	 	'editors/',			'Editor');
		$resourceLoader->addResourceType('view', 		'views/',			'View');
		$resourceLoader->addResourceType('plugin', 		'plugins/',			'Plugin');
		$resourceLoader->addResourceType('Object', 		'objects/',			'Object');
		$resourceLoader->addResourceType('model', 		'models/',			'Model');
		$resourceLoader->addResourceType('form', 		'forms/',			'Form');
		$resourceLoader->addResourceType('acl', 		'acls/',			'Acl');
		
		// Now we are sure all namespaces are defined
		defined ('APP_NAMESPACE_INIT') || define ('APP_NAMESPACE_INIT', 145);
				
		
		Zend_Controller_Front::getInstance()->addControllerDirectory(array('default' => APPLICATION_PATH . '../library/Point/Plugins/'))
											->setRouter(new Zend_Controller_Router_Rewrite())
											->registerPlugin(new Point_Plugins_Controllers_ViewSetup());
											
		/**
		 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		 * =========================================
		 * Login from cookie is handled by 'Point_Plugins_Controllers_ViewSetup'
		 * =========================================
		 */
													
		/**
		 * setup Placeholder hooks for our application
		 */
		
		Zend_Controller_Front::getInstance()->registerPlugin(new Point_Plugins_Controllers_ActionsSetup(),98);
		
				
		/**
		 * Setup the template
		 * -----------------------------
		 */	
		// Get the current template
		$defaultPath = realpath(APPLICATION_PATH .'/../templates/');
		$templateName = 'current_template';
//	    $template = Point_Model_Page_Templates::getInstance()->setup($defaultPath, $templateName);
	    
	    /**
	     * 	Setup the Editor's broker
	     * -----------------------------
	     */
	    $optionsEditors	= array('path' => APPLICATION_PATH . '/usr/editors/');	
	    Point_Editor_Broker::getInstance()->setup( $optionsEditors );
	    
		/* Declare our storage namespace */
		Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('churchpt'));
		defined('APP_SESSION_NAMESPACE') || define ('APP_SESSION_NAMESPACE', 'point');
		
		Point_Object_Session::getInstance();
		
		/* Define Access list */
		Point_Acl_Views::getInstance();
		
		
		
	}
	
	
	protected function _initConfig()
    {
        $config = $this->getConfig();
        Zend_Registry::set('config', $config);
        return $config;
    }
	
	
	public function _initAllCache()
	{
			$pageCacheOptions 	= array(
										'frontend' 			=>  'Core',
										'backend'  			=>	'File',
										'frontendOptions' 	=> array('lifeTime' => 5, // 20 mins 
																	 /*'automatic_seralization' => true*/),
										'backendOptions' 	=> array('cache_dir' 		=> APPLICATION_PATH .'/cache/',
																	 'file_name_prefix' => 'yinkakunle_'));
			Point_Model_Page_Cache::getInstance()->setup ($pageCacheOptions);	
	}
	
	protected function _getVal($name)
	{
		
		if (isset($this->view))
		{
			return $this->view;
		}
		else  /*if ($this->hasResource($name))*/
		{
			$this->bootstrap($name);
			$this->view = $this->getResource($name);
			return $this->view;
		}
		
	}
	
	
	function _initDoctype()
	{
		if($this->_getVal('view'))
			$this->view->doctype('XHTML1_RDFA');
	}
	
	function doFirst()
	{
		return $this;
	}
	
	
	public function run($options = null)
	{
//		/* If not ajax call then just kick off from there */
//		$request = Zend_Controller_Front::getInstance()->getRequest();
//		if (!$request->isXmlHttpRequest())
//    	{
    		//Let's stat our MVC
			$configs		= $this->getOption('resources');
			Zend_Layout::startMVC($configs['layout']);
//    	}

		parent::run($options);

	}
	
	protected function getConfig()
	{
		if (null === $this->config)
		{
			$config	 		= new Zend_Config($this->getOptions(), 'appinfo');
			
			$this->config 	= $config; 
		}
		return $this->config;
	}

}


