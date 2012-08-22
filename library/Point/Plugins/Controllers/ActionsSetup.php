<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: ActionSetup.php
 * 
 * Created by ashon
 * 
 * Created on Aug 9, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
class Point_Plugins_Controllers_ActionsSetup extends Zend_Controller_Plugin_Abstract
{
	public function dispatchLoopStartup (Zend_Controller_Request_Abstract $request)
	{
		if ($request->isXmlHttpRequest())
    	{
    		return;
    	}
		global	$timer;
		$timer	= microtime_float();
		$front	= Zend_Controller_Front::getInstance();
		
		/* Ensure we have a valid Zend_Controller_Plugin Action Stack first!!! */
		if (!$front->hasPlugin('Zend_Controller_Plugin_ActionStack')){
			$actionStack = new Zend_Controller_Plugin_ActionStack();
			$front->registerPlugin($actionStack, 94);
		}else{
			$actionStack = $front->getPlugin('Zend_Controller_Plugin_ActionStack');
		}

		
		
		// Check if request is not XML_Request
		if (!$request->isXmlHttpRequest())
		{
			/**
			 * Creat the a placeholder for the menu
			 * ------------------------------------
			 */
			$menuAction	= clone ($request);
			$menuAction->setControllerName('Xpage')->setActionName('menu');
			$actionStack->pushStack($menuAction);
			
			/**
			 * Creat the a placeholder for the footer
			 * ------------------------------------
			 */
			$footerAction = clone ($request);
			$footerAction->setControllerName('Xpage')->setActionName('footer');
			$actionStack->pushStack($footerAction);
			
			$loginAction = clone ($request);
			$loginAction->setControllerName('account')->setActionName('user')->setParam('inline', true);
			$actionStack->pushStack($loginAction);
		}
		
		else
		{
			// It's Ajax request
			// Disable layout...
			Zend_Layout::getMvcInstance()->disableLayout();
		}
	}
	
}