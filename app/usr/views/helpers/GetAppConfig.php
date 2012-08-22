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
class Point_View_Helper_GetAppConfig {
	/**
	 * @ver AppName
	 */
	protected $_appName	 = null;
	/**
	 * @var version
	 */
	protected $_version  = null;
	/**
	 * @var mainName	Main site name
	 */
	protected $_mainName = null;
	/**
	 * @var altName; 	Alternative name for Site (Usually in another language)
	 */
	protected $_altName	 = null;
	/**
	 * @var	slogan		Church's Slogan
	 */
	protected $_slogan   = null;
	/**
	 * @var config
	 */
	protected $_config   = null;
	/**
	 * @var configArray
	 */
	protected $_configArray = null;
	
	function __construct()
	{
		$this->_config	= new Zend_Config_Xml(APPLICATION_PATH .'/configs/xml/churchpoint.xml', 'application');
		$this->_configArray	= $this->_config->toArray();	// Store Array Alternative
		$this->mainName =  trim($this->_config->churchname);
		$this->altName =  trim($this->_config->altname);
		$this->slogan =  trim($this->_config->slogan);
		$this->appName =  trim($this->_config->appname);
		$this->version =  trim($this->_config->version);
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