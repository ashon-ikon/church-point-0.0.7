<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 21, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Scripts
{
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	private function __construct() {	}
		
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	public function getCompressedJScript($view_obj)
	{
		if (null === $view_obj)
			throw new Exception('The view object passed cannot be empty');
		/* Echo the content inside a variable */
		ob_start();
		
		echo $view_obj->headScript();
		
		$js_content 	= ob_get_contents();
		
		ob_end_clean();
		
		/* get each script */
		$pattern		= '@<script\b[^>]*>([\w\S\s]*?)</script>@i';
		$matches		= null;
		
		if (preg_match_all($pattern, $js_content, $matches))
		{
			$src_pattern = '@src=("[a-zA-Z0-9/_.:-]*"|\'[a-zA-Z0-9_.:/-]*\')@i';
			foreach ($matches[0] as $match)
			{
				/* extract the link */
				$src_matches	= null;
				$script_link	= $match;
				if(preg_match($src_pattern, $script_link, $src_matches))
				{
					if ($link = $src_matches[1])
					{
						$link	=	ltrim($link, '\'');
						$link	=	ltrim($link, '"');
						$link	=	rtrim($link, '\'');
						$link	=	rtrim($link, '"');
						echo "\n",$link;
					}
					
				}
				else
				{
					/* Check if script link has content and output retrieve that */
				}
				
			}
			echo '<pre>', print_r($matches, true),'</pre>';exit;
		}
	}
	
}