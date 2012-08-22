<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 31, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Editor_Broker
{
	
	/**
	 * Hold the editors in registry
	 */
	protected $_editors = array();
	
	
	protected $_basePath = null;
	
	protected static $instance 	= null;
    /**
     * Prevent further cloning
     * ----------------------
     */
    private function __construct()
    {    

    }
		
	public static function getInstance()
	{
		if ( null === self::$instance )
		{
			self::$instance = new self; 
		}
		
		return self::$instance;
	}
	
	/**
	 * Basic information must be set here
	 * 		-> Editors' path (array('path' => 'path/to/editors/'))
	 * 		-> 
	 * --------------------------------
	 */
	public function setup ($options)
	{
		$path = null;
		if (is_array($options) && array_key_exists('path', $options))
		{
			$path = $options['path'];
		}else
			$path = $options;
		if (null === $path)
			throw new Zend_Exception ('Path to Editors can not be empty!');
			
		//We made it here.. Let's store the path
		$this->_basePath = remove_trailing_slash($path);
		
		return $this;
	}
	
	/*
	 * Loads editor if file exists. 
	 * -------------------------------------
	 */
	public function &getEditor($editorName, $options = null, $prefix = 'Point_Editor_')
	{
		// Ensure we have valid options | read from xml
		$optionsXMLPath = $this->_basePath . DS . $editorName ; 
		if (null === $options && readable($optionsXMLPath))
		{
			$optionXML = new SimpleXMLElement($optionsXMLPath, null, true);
			$options= $xml->editor->options;
		}
		
		$editorName = $prefix . ucfirst($editorName);
		if (!array_key_exists($editorName, $this->_editors))
			$this->_editors[$editorName] = new $editorName ($options);
		 
			
		return $this->_editors[$editorName];
	}
	
	public function __get($d)
	{
		
	}
}