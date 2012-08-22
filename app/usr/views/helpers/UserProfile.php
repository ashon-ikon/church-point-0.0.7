<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: UserProfile.php
 * 
 * Created by ashon
 * 
 * Created on Aug 8, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
class Point_View_Helper_UserProfile{
	public function userProfile()
	{
		$frontController	= Zend_Controller_Front::getInstance();
		echo '<span><b>From userProfile() helper</b></span>';
	}
	
}
