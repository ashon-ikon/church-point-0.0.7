<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	Point_Object_Page
 * Created by ashon on Sep 30, 2011
 * (c) 2010 - 2011 Copyright
 * 
 * Depends on Point_Model_ContentGroup
 * Depends on Point_Model_User
 * Depends on Point_Object_Menu
 * -------------------------------------------
 */
class Point_Model_Page   extends	Point_Model_BaseClass
{
	/**
	 * @var db_table 
	 * Holds Pages Table handle
	 */
	protected			$_db_table		= null;
	/**
	 * @var string templatelocation
	 */
	protected			$_template_path = APP_PAGE_TEMPLATES;
	
	/**
	 * Groups Table name
	 */
	protected			$_table_name 	= 'pages_table';
	
	
	protected			$_top_menu		= TOP_PAGE_ID;
	
	
	
	
	/**
	 * Singleton instance holder
	 */
	private static		$_instance;
	
	
	private function __construct()
	{
		 
	}
	
	/**
	 * Get the singleton instance to Menu Object
	 */	
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	/**
	 * Get Database table
	 * 
	 * This method retrieves the 
	 */
	public function getDbTable()
	{
		if (null === $this->_db_table)
		{
			$db	= new Point_Model_DbTable_DbCore($this->_table_name);
			$this->_db_table = $db->getDBTable();
		}
		return $this->_db_table;
	}
	/**
	 * This retrieves the contents for the specified page
	 * 
	 * The each agent will be contacted
	 * 
	 * @param	String	Name of page to retrieve
	 * 
	 */
	public function getContents($page)
	{
		/* Retrieve the info for the sent page */
		$template 		= $this->_getTemplate($page);
		
		$parsedContent	= $this->_parseContent($template); 
		
		echo $parsedContent;				
		return;
	}
	
	/**
	 * This retrieves the reads the template
	 */
	protected function _getTemplate($page, $path = null)
	{
		/* Ensure we have a valid path */
		if (null === $path)
		{
			$path = $this->_template_path;			
		}
		if (!readable($path))
			throw new Exception('Unable to read template for \''.$path.'\'.');
		
		/**
		 * Use the Page Template object
		 */
		$template_obj	= Point_Model_Page_Templates::getInstance();
		/* Setup the template */
		$template_obj->setup($path);
		
		$template 		= $template_obj->getTemplate($page);
		
		return $template;
		
	}
	
	/**
	 * Parse Content and substitute content
	 */
	protected function _parseContent($template)
	{
		/* Get each of the agents */
		$agentBroker	= Point_Model_Agent_Broker::getInstance();
		
		$agents			= $agentBroker->extractAgents($template);
		
		/* for each agent get html content */
		$agent_contents	= null;
		
		$parsedContent	=	null; 
		
		foreach ( $agents as &$agent )
		{
			/**
			 * --------------------------------------------
			 * Ensure all Agent classes are loaded first!
			 */			
			$agent_class_name 	= ucfirst($agent['agent']) . 'Agent';
			
			$agent_class 		= null;
			if (!class_exists($agent_class_name, true))
			{
				/* Try using a proper class name */
				$agent_class_name	= 'Point_Model_Agent_'.$agent_class_name ;
				
				if( !class_exists($agent_class_name, true))
				{
					/* throw exception: Unknown class */
					throw new Exception ('Unable to load: '. $agent_class_name);	
				}
				else
				{
					/* Update the class name */
					$agent['agent']		= $agent_class_name;
				}
			}
			
			$agent_class		= new $agent_class_name();
			/**
			 * ------------------------------------------
			 * Get content of each agent and replace in main 
			 * template
			 */
			
			$parsedContent	 = str_replace($agent['haystack'], $agent_class->makeContent() , $template );
//			$parsedContent	 = preg_replace('/'.$agent['haystack'].'/',$agent_class->makeContent(), $template);
			
			/* Modify template */
			$template 		 = $parsedContent;
		}
		
		return $parsedContent;
	}
}
	

 