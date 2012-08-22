<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Dec 27, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
/**
 * Depends on Point_Model_BaseClass
 */
class Point_Object_Cookie extends Point_Model_BaseClass
{
	
	protected	$_cookie_duration= COOKIE_AGE;
	
	protected static $_instance = null;

	private function __construct() { }
	
	public static function getInstance()
	{
		if ( null === self::$_instance )
		{
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	public function init()
	{
		
	}
	
	/**
	 * This sets information into the user browswer's cache
	 * @param	$data		Array(
	 * 				'name' 		=> String				
	 * 				'value' 	=> String | Serialized Data				
	 * 				'duration' 	=> Integer		
	 * 			)	
	 * 
	 * 	Throws exception when no name is specified
	 */
	
	public function set($data = array(), $encrypt = true)
	{
		$name = $value = $duration = null;
		
		if(array_key_exists('name', $data))
			$name = $data['name'];
		else
			throw new Zend_Exception('Cookie name must be specified');
			
		if(array_key_exists('value', $data))
			$value = $data['value'];
			
		if(array_key_exists('duration', $data))
			$duration = $data['duration'];
						
		if (null === $duration)
		{	$duration = $this->_cookie_duration; }
		
		/* Do the setting of the cookie here */

		$check = null;
		if( null !== $value)
		{
			$request 	= Zend_Controller_Front::getInstance()->getRequest();
			if ($encrypt)	$value 	= encrypt($value, APP_SALT);
			$check 		= encrypt($request->getClientIp(), APP_SALT);
		}
		setcookie($name.'_check', serialize($check) , time() + $duration, '/');
		setcookie($name, serialize($value), time() + $duration, '/');

		//setcookie('name','value',3, 'path', 'domain', 'secure', 'http');
//		echo 'cookie set as: <b>',$name,'</b> and value <b>',$value,'</b>'; exit;
	}
	
	/**
	 * This gets information into the user browswer's cache
	 * @param	$name 	String
	 */
	public function get($name, $decrypt = true)
	{

		$request 	= Zend_Controller_Front::getInstance()->getRequest();

		if (isset($_COOKIE[$name]) && unserialize(cleanSlashes($_COOKIE[$name.'_check'])) == encrypt($request->getClientIp(), APP_SALT))
		{
			$val = unserialize(cleanSlashes($_COOKIE[$name]));
			if ($decrypt)
			{		
				$val = decrypt($val, APP_SALT);
			}
			
			return $val;
		}
	}
	
	/**
	 * This forces the data to be marked obsolete by the user browswer
	 * @param	$name 	String
	 */
	public function remove($data)
	{
		$this->set($data, - (time() + $this->_cookie_duration) );
		$this->set($data.'_check', - (time() + $this->_cookie_duration) );
	}
	
	
}