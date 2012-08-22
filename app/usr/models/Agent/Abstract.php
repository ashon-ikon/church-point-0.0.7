<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 23, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
abstract class Point_Model_Agent_Abstract
{
	protected	$_viewObject		= null;
		
	/**
	 * method	getView()
	 * @return ZendView view object that can be rendered into OR null
	 */
	protected function _getView()
	{	
		/* Rase an alarm if we don't have a valid view */
		if (null === $this->_viewObject)
    	{
    		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	    	if (!$this->_viewObject = $viewRenderer->view)
	    	{
		    	throw new Exception ('Call to empty view!');
	    	}
	    	
    	}
		
		return $this->_viewObject;	
	}
	
	protected function _getBaseUrl()
	{
		$request 		= Zend_Controller_Front::getInstance()->getRequest();
		$base_url		= $request->getScheme(). '://'. $request->getHttpHost();
		
		return $base_url;
	}
	
}