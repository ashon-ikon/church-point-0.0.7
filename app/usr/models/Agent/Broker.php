<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 18, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Agent_Broker
{
	
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
	 * =====================================================
	 * Extracts Agents from stream
	 * 
	 * @param string stream
	 * @return	array	all matching agents. 
	 * =====================================================
	 */
	public function extractAgents($stream = null)
	{
		if (null !== $stream)
		{
			/* extract all anotated parts */
			
			//   {{%sermon:wordquote1%}}
			
			$sections		= array();
			$pattern 		= '/({{%)[a-z]+:[a-zA-Z0-9_-]+(%}})/i';
			$read_contents 	= array();
			
			if ($agent_info 	= $this->_getSections($stream, $pattern))
			{			
				/* Return the details */
				return $agent_info;
			}
		}
	}
	
		
	/**
	 * Here we perform some preg matching ;)
	 * {{%articles:section1%}}
	 *	/({{%)[a-z]+:[a-zA-Z0-9_-](%}})/
	 *	@return array array(editor , marker)
	 */
	public function _getSections($stream , $pattern)
	{
		$editor_marker = array();
	
		
		if (!empty($stream) && $pattern !== null)
		{
			
			$ma = null;
			preg_match_all($pattern, $stream, $ma);
				
			if(is_array($ma))
				foreach ($ma[0] as $m)
					$markers[] = $m;
		}
			else
				throw new Zend_Exception('Empty stream or invalid pattern passed!');

			
		foreach ($markers as $haystack)
		{
			// Get editor & marker
			$pos 	= 	strpos($haystack,':');
			$editor	=	substr($haystack, 0 , $pos);
			$editor =	str_replace(array('{','%','}'), '', $editor);
			$marker =	strstr($haystack, ':');
			$marker =	str_replace(array(':','%','}'), '', $marker);
			
			$editor_marker[] = array('agent' 		=> $editor , 
									 'marker'		=> $marker,
									 'haystack'  	=> $haystack);
		}
		
		return $editor_marker;
	}
}
