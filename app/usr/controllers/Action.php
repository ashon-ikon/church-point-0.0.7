<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: action.php
 * 
 * Created by ashon
 * 
 * Created on Aug 2, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
class Point_Controller_Action extends Zend_Controller_Action
{
	/**
	 * @var Point_Model_Page
	 */	
	protected	$_page	= null;
	
	protected	$_page_info;
	
	/**
	 *  Handle Unknown calls 
	 */
	public function __call($method, $args)
    {
       if ('Action' == substr($method, -6)) {
           // If the action method was not found, render the error
           // template
	            
           // Let's go to the default url if it exists
           $url 	= '/'. $this->getRequest()->getControllerName() . '/index';
           return $this->_redirect($url);
       }
	 
      // all other methods throw an exception
       throw new Exception('Invalid method "'
                            . $method
                            . '" called',
                            500);
    }

	public function init()
	{
		$this->_page	= Point_Model_Page::getInstance();
	}
	
	public function preDispatch()
	{
			
	}
	
	protected function _setTitle($title, $escape = true)
	{
		if ($escape)
			$title	 = $this->view->escape($title);
			
		$this->view->headTitle($title, 'APPEND');
    	$this->_helper->layout()->page_title = $title;
	}
	
	protected function _getBaseUrl()
	{
		$request			= $this->getRequest();
		
		return $request->getScheme() . '://' . $request->getHttpHost();	
	}
	
	protected function _setPageDescription($description, $escape = true)
	{
		$layout		= Zend_Layout::getMvcInstance();
		
		$layout->page_desciption = $escape? $this->view->escape($description) : $description;	
	}
	
	protected function _disableLayout()
	{
		$layout		= Zend_Layout::getMvcInstance();
		
		$layout->setLayout('ajax');
		
		defined('LAYOUT_OFF')
			|| define('LAYOUT_OFF', 444);	
	}

	protected function _enableSocialSharing()
	{
		$layout		= Zend_Layout::getMvcInstance();
		
		$layout->enable_sharing	= true;
		
		defined('ENABLE_SHARE')
			|| define('ENABLE_SHARE', 555);	
	}
}