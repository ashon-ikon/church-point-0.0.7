<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 4, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Object_Session
{
	protected static $instance;
	
	private function __construct()
	{
		
	}
	
	// This ensures that the session variable is available
	public static function getInstance()
	{
		if (null === self::$instance)
		{
			// Use the global namespace
			if (!defined('APP_SESSION_NAMESPACE')) 
				throw new Zend_Session_Exception;
				
			self::$instance = new Zend_Session_Namespace(APP_SESSION_NAMESPACE, true); // make it singleton
		}
		return self::$instance;
	}
}