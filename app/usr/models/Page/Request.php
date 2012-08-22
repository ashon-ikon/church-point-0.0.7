<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 20, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */

/*
 * DEPENDS ON Point_Model_Page_Template
 */
class Point_Model_Page_Request 
{
	protected	$_params			= array();
	protected	$_templateExtension	= null;
	
	public function __construct()
	{
		/* Set the extension based on the template's extension */
		$this->_templateExtension = Point_Model_Page_Templates::getInstance()
															->getExtension();
	}
	
	/*
	 * $request	Array()
	 */
	public function setParams($request)
	{
		// Ensure we truly have an array-like request
		if (!is_array($request))
		{
			throw new Zend_Exception ('Invalid Request received');
		}
		$this->_params		= $request;
		
		return $this;
	}
	
	/*
	 * Get all the parameters
	 */
	public function getParams()
	{
		if (isset($this->_params))
			return $this->params;
	}
	
	/*
	 * Get a particular parameter
	 */
	public function getParam($name , $default = null)
	{
		// Check if we have the parameter then give 
		// Else return default
		if (array_key_exists($name , $this->_params))
			return $this->_params[$name];
		else
			return $default;
 
	}
	
	// Getter function
	public function __get($name)
	{
		// Check if we have the parameter then give
		if (array_key_exists($name , $this->_params))
			return $this->_params[$name];
	}
	
	/*
	 * Build a requested page name based on the parameters
	 */
	public function getComputedPageName()
	{
		$filename = '';
		$filename .= $this->module 		. DS ;
		$filename .= $this->controller 	. DS ;
		$filename .= $this->action 		. DS ;
		 
		// append the time info
		$filename .= $this->iw ;
		
		return $filename;
	}
}