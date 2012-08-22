<?php
/**
 * This controller helps with page specifics suchas
 * 		header, footer, menu
 *
 */
class XpageController extends Point_Controller_Action
{
	protected 	$_request = null;

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function preDispatch()
    {
    	$front	= Zend_Controller_Front::getInstance();
    	$this->_request = $front->getRequest();
    	
    	/**
    	 * We are goind to be rendering into
    	 * Zend_Layout::placeHolder()->actionName
    	 * so in layout script it will be:
    	 * 
    	 * 		$this->layout()->action-name
    	 */ 
       	$viewRenderer	= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
       	$viewRenderer->setResponseSegment($this->_request->getActionName());
    }

    public function indexAction()
    {
    	// This page SHOULD NOT be assessible
        $this->_redirect('/');
    }

    public function headerAction()
    {
        // action body
    }

    public function footerAction()
    {
	    $config						= Zend_Registry::get('config')->app;
		$this->view->social_links	= $config->social->toArray();
	//	echo '<pre>',print_r($this->view->social_links,true),'</pre>';
    }

    public function menuAction()
    {
    	/* Read the menus from menus database */
    	/* Read all the top-level pages */
		$menus		= Point_Object_Menus::getInstance();
		$session 	= Point_Object_Session::getInstance();
		$url		= $session->in_url;

		$this->view->menus 	= $menus->getMenus(false, array('url' => $url));	
       // action body
    }


}


/* Zend_CodeGenerator_Php_File-DocblockMarker */


