<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 20, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Model_Page_Templates 
{
	protected 	$_extension 			= '.phtml';
	protected 	$_templates				= null;
	protected	$_templates_dir			= null;
	protected	$_templates_filename 	= null;
	protected	$_current_tpl_root		= null;
	
	protected static $instance 			= null;
	
	/* Disallow new instancing 'Singleton'*/
	private function __construct()
	{
		
	}	
	
	public static function getInstance()
	{
		if (null === self::$instance)
		{
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/**
	 * 	EXTENSION
	 * --------------------
	 */
	public function getExtension()
	{
		return $this->_extension;
	}
	
	public function setExtension($extension)
	{
		$extension = trim ( $extension );
		if ('.' != $extension[0]) // if there is no '.' at the begining add it
		{	$extension = '.' . $extension; }
		
		$this->_extension = $extension;
	}
	
	
	/**
	 * 	Template
	 * --------------------
	 */
	public function getTemplate ($page)
	{
		// Check if page exists
		
		foreach ($this->_templates as $key => $template)
		{
			// Construct location...
			$location = remove_trailing_slash($this->_templates_dir) . DS . 
						remove_trailing_slash($this->_current_tpl_root). DS . 
						$template['page'] . $this->getExtension();
						
			if ($page == $template['page'] && readable($location))
			{				
				return file_get_contents($location);
			}
//			// Use default 'index'
//			else if ('index' == $template['page'] && readable($location))
//			{				
//				return file_get_contents($location);
//			}
		}
		
	}
	
	
	public function getTemplateDir()
	{
		return $this->_templates_dir;
	}
	
	public function setup ($templateDir, $templateName = 'current_template')
	{
		$this->_templates_dir = (substr($templateDir, -1) == DS ? $templateDir :  $templateDir . DS); // Ensuring only one trailing slash
		
		$this->_templates_filename = $templateName;
		
		//read the info from the file...
		$this->readTemplateInfo($this->getFullTemplatesPathname());
		
		return $this;
	}
	
	public function getFullTemplatesPathname ()
	{
		return $this->_templates_dir . $this->_templates_filename . '.xml';
	}
	
	
	/**
	 * 	Reading and Writing to file
	 * --------------------
	 */
	protected function readTemplateInfo($path)
	{
		$templates = null;
		/* try to read from file */
		if (readable($path))		
		{
			$xml	= new SimpleXMLElement($path, null, true);
			
			// Get the template root
			$this->_current_tpl_root = $xml->content->rootpath;
			
			foreach ($xml->content->template as $templt)
			{
				$templates[]	= (array)$templt;
			}
		}
		
		if( $templates )	$this->_templates = $templates;
		
	}
	
}
