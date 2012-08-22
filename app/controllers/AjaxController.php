<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 1, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class AjaxController extends Point_Controller_Action
{
	protected	$_template_content;
	protected	$_user;
	protected	$_XmlResponse 	= null;
	
	/**
	 * request types
	 */
	const AJAX_REQUEST_CMS		= 'cms';
	const AJAX_REQUEST_COMMENT	= 'comment';
	const AJAX_REQUEST_ABLUMS	= 'albums';
	const AJAX_REQUEST_PICTURE	= 'picture';
	
	
    public function init()
    {
    	$this->getResponse()
                   ->setHeader('Cache-Control',
                               'no-cache, no-store, max-age=0, must-revalidate, post-check=0, pre-check=0')
                   ->setHeader('Expires', 'Tue, 14 Aug 1997 10:00:35 GMT');

    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('request', 'html')
    				->initContext();
		// Both layout and view renderer should be disabled
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
        Zend_Layout::getMvcInstance()->disableLayout();
    }

    public function indexAction()
    {
    	
        // action body
        $this->_forward('request');
    }
    
    protected function _getXmlResponse()
    {
    	if ( null === $this->_XmlResponse )
    	{
    		$this->_XmlResponse = new Point_Object_AjaxResponse();
    	}
    	return $this->_XmlResponse;
    }
    
    /**
     *  @return:  must be XML | false!
     */
    public function requestAction()
    {
    	
    	if (!$this->getRequest()->isXmlHttpRequest())
    	{
    		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			$redirector->gotoUrl('error/')->redirectAndExist();
    	}
    	
  
//		file_put_contents(APPLICATION_PATH . '/test.log',"\n".print_r($this->getRequest()->getParams(), true), FILE_APPEND);
//    	file_put_contents('/home/ashon/var/test.log',print_r($this->getRequest()->getParams(), true));
    	// Check the if user is logged on
    	$auth= Zend_Auth::getInstance();
    	if (!$auth->hasIdentity())
    	{
    		// Unknown user
    		return $this->_getXmlResponse()->postError('Authenication: Unknown user', 4); 
    	}
  		
    	/**
    	 * All seems okay... 
    	 * 
    	 * Dispatch the ajax request
    	 */
    	
    	$this->_dispatch();
  
    }
	
	/**
	 * This dispatches the request of the ajax
	 */
	protected function _dispatch( )
	{
		$request = $this->getRequest();
		
		// Check if we know the request type
    	if (null === ($request_type = $request->getParam('req',null)))
    	{
    		return $this->_getXmlResponse()->postError('Unknown request', 2);	
    	}
		
		switch ($request_type)
		{
			case self::AJAX_REQUEST_CMS:
			{
				// Check if we have the ids
    			if (null === ($ids = $request->getParam('id',null)))
		    	{
		    		return $this->_postError('Invalid content id', 3);	
		    	}
		    	$ids = substr($ids, 4);
		    	
				// Re-Compute hash and check
				$computedHash	= makeUniqueHash( $ids );
				$hash 			= $request->getParam('hash', null); 
				
//				file_put_contents('/var/www/php-stuff/tests.log',"\nSTARTING\n". print_r($this->getRequest()->getParam('cont'), true) , FILE_APPEND);
				
				if ($computedHash != $hash)
				{
					return $this->_postError('Something went wrong internally. Please try again', 4);
				}
				
				
				$real_ids = getTrueItem($ids); 
				
				// write the content
				$cms = new Point_Model_Page_Contents();
				
				//=========================FORMAT================================
				//Point_Model_Page_Contents::writeContent($content, $hash, $ids , $new = false)
				//===============================================================
				$ret_content = $success = $msg = null;
				list($success, $msg) = $cms->writeContent(
								$request->getParam('cont', null),  
								$request->getParam('hash', null),
								$real_ids,
								$request->getParam('new', null));
//				file_put_contents('/var/www/php-stuff/tests.log',"\n***". $msg, FILE_APPEND);
				if($success)
				{
					return $this->_postContent(null);	
				}
				else
					return $this->_postError($msg, 6);
				
			}break;
			
			case self::AJAX_REQUEST_ALBUMS:
			{
				
			}
			break;
			
			case self::AJAX_REQUEST_PICTURE:
			{
				
			}
			
			break;
//			default:
//			{
//				$this->_postError('Request: Undefined request');
//			}
		}
	}
	
	protected function _postContent($msg)
	{
		$this->_getXmlResponse()->postContent($msg);
	}
	
	protected function _postError($msg, $err_no = 0)
	{
		
		$this->_getXmlResponse()->postError($msg, $err_no);
	}    
}