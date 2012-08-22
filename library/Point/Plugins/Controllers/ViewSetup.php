<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: ViewSetup.php
 * 
 * Created by ashon
 * 
 * Created on Aug 8, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
class Point_Plugins_Controllers_ViewSetup extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @var Zend_View
	 */
	protected	$_view;
	
	/* User info */
	protected	$_user	= null;
	protected	$_role	= null;
	
	protected	$_noauth = array(
							'module' 	=> 'default',
							'controller'=> 'error',
							'action'	=> 'noaccess'
							);
	
	/**
	 * Page caching stuff
	 */
	protected	$_cacheKey	= null;
	protected	$_noCache	= false;
	protected	$_pageCache	= null;
	
	
	
	private	function _getUser()
	{
		if (null === $this->_user)
		{
			$this->_user	= Point_Model_User::getInstance();
		}
		return $this->_user;
	}
	
	
	public function routeStartup (Zend_Controller_Request_Abstract $request)
	{
		
	}
	
	public function routeShutdown (Zend_Controller_Request_Abstract $request)
	{

	}
	/**
	 * Injecting our own view helper classes
	 */
	public function dispatchLoopStartup (Zend_Controller_Request_Abstract $request)
	{
		if ($request->isXmlHttpRequest())
    	{
    		return;
    	}
		
		/**
		 * -----------------
		 * Try to login through cookie if possible
		 * ----------------
		 */
		$user 	= Point_Model_User::getInstance();
		$cookie = Point_Object_Cookie::getInstance();
		
		if(($u_id = $cookie->get('tu13')) && !$user->isLoggedIn())
		{
			$user 	= Point_Model_User::getInstance()->authenticateById($u_id);	
		}
		 
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		
		$view = $viewRenderer->view;
		
		$this->_view = $view;
		
		$view->orginalModule = $request->getModuleName();
		
		$view->orginalController = $request->getControllerName();
		
		$view->orginalAction = $request->getActionName();
		
		$helperName			 = 'Point_View_Helper';
		
		$view->addHelperPath(APPLICATION_PATH . '/usr/views/helpers', $helperName);
		
		$request				= $this->getRequest();
		/* Get the incoming di*/
		$controller 			= $request->controller;
	    $action 				= $request->action;
	    $module 				= $request->module;
		$resource 				= $controller;
		$role					= $this->_getUser()->getRole();
		
		$acl					= Point_Acl_Views::getInstance();
		
		if (!$acl->has($resource)) {
        	$resource = null;
        	
        	$request->setControllerName($controller);
			$request->setActionName($action);
			
			/* Set up the new route */
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
	   		$redirector->gotoUrl('error/nopage')->redirectAndExist();
	   		return;
    	}	
		
		
//		echo ($acl->isAllowed($this->_getUser()->user_role, $resource, $action) ? 'Allowed': 'Not allowed'), "<br />\n";
		
		// Store the last url
		$url		= $request->getScheme() . '://' . $request->getHttpHost() . $request->getRequestUri();
		if($controller.'/'.$action != 'account/login')
		{
			$reg	= Point_Object_Session::getInstance();
			$reg->incoming_uri = $this->getRequest()->getRequestUri();
		}
		if(!$acl->isAllowed($role, $resource, $action))
		{
			if (!$this->_getUser()->isLoggedIn()) {
				//User is a guest ask him to authenticate him/herself
				
                $controller = 'account'; //$this->_noauth['controller'];
                $action 	= 'login';	 //$this->_noauth['action'];
                $action		.= '?url=' . urlencode($url);

					
			} else {
				// Not a guest but... sorry you are not allowed :(
				$controller = $this->_noauth['controller'];
                $action 	= $this->_noauth['action'];
				
			}
			
			
			$request->setControllerName($controller);
			$request->setActionName($action);
			
			/* Set up the new route */
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
	   		$redirector->gotoUrl($controller.'/'.$action)->redirectAndExist();
		}

		/**
		 * Check if page exists then spin off from there...
		 * --------------------------------------------------
		 */
		$this->_cacheKey = md5( implode($request->getParams()) );
		$this->_noCache	= !$request->isGet();	// True only if it's  not get request 
		$this->_pageCache 	= Point_Model_Page_Cache::getInstance();
		$response	= $this->_pageCache->fetch($this->_cacheKey, $this->_noCache);
		

		if (false !== $response)
		{
			
//			echo $response;
//			exit; // kill script
			
		}
		
				
	}
	
	public function preDispatch (Zend_Controller_Request_Abstract $request)
	{
				

	}
	
	public function postDispatch (Zend_Controller_Request_Abstract $request)
	{
		
	}
	
	public function dispatchLoopShutdown()
	{
		global $timer;
		if (!$this->getRequest()->isXmlHttpRequest())
			$this->getResponse()->appendBody( number_format((microtime_float() - $timer), 3)  . ' seconds' );
		if (!$this->_noCache && $this->_pageCache)
		{
			// Store the page inside cache ;)
			$this->_pageCache->getCache()->clean(Zend_Cache::CLEANING_MODE_OLD);
			$this->_pageCache->getCache()->save($this->getResponse()->getBody(), $this->_cacheKey);
		}
	}

}