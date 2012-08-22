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
class Point_View_Helper_FullUrl extends Zend_View_Helper_Url{
	
	
	public function fullUrl(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
	{
		
		$request 	= Zend_Controller_Front::getInstance()->getRequest();
		
		$ret_url		= $request->getScheme(). '://'. $request->getHttpHost() . $this->url($urlOptions, $name, $reset, $encode);
		return $ret_url;
	}
}