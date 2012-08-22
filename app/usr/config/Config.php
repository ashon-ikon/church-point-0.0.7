<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 22, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Config_Config
{
	/**
	 * @var config
	 */
	protected 	$_config   	= null;
	
	/**
	 * @var string config filename
	 */
	protected	$_filepath	= null;
	
	/**
	 * @var configArray
	 */
	protected $_configArray = null;
	
	protected static $instance = null;
	
	/* Disallow new instancing 'Singleton'*/
	private function __construct()
	{
//		echo print_r($this->_filepath, true);
		
//			
	}	

	public function init($options, $section  = null)
	{
		$path = null;
		
		if (null !== $options)
		{
			if (is_array($options) && array_key_exists('path', $options))
			{
				$path 	= $options['path'];
			}
			else if (is_string($options) && is_file($options))
			{
				$path	= $options;
			}
			
			$this->_filepath = $path; // Store the path	
		}
		else
		{	
			throw new Zend_Exception('Empty config path sent !'); 
		}
		
		$this->_config	= new Zend_Config_Xml($this->_filepath, $section);
			
	}
		
	public static function getInstance()
	{
		if (null === self::$instance)
		{
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	
	public function __set($name, $value)
	{	$var = '_' . $name;
		if (array_key_exists($var, get_class_vars(get_class($this)))){
			Zend_Registry::getInstance()->set($var, $name);
			$this->$var	= $value;
		}
	}
	
	public function __get($name)
	{
		$var = '_' . $name;
		if (array_key_exists($var, get_class_vars(get_class($this)))){
			// Check if we have the data first else get from registry
			if (null === $this->$var)
			{
				if (Zend_Registry::getInstance()->isRegistered($var)){
					$this->$var = Zend_Registry::getInstance()->get($var);
				}else{ // We need to get it from database...
					$this->$var = 'No Registry entry for \' ' . $var . '\' ';
				}
			}
			return $this->$var;
		}	
	}
	
	public function getAppConfig($options = null)
	{
		return $this;
	}
}