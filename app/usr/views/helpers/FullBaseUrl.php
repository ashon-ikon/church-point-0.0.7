<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: AppConfig.php
 * 
 * Created by ashon
 * 
 * Created on Aug 9, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
class Point_View_Helper_FullBaseUrl extends Zend_View_Helper_Url{
	
	
	public function fullBaseUrl()
	{
		
		$request 	= Zend_Controller_Front::getInstance()->getRequest();
		
		$ret_url		= $request->getScheme(). '://'. $request->getHttpHost();
		return $ret_url;
	}
}