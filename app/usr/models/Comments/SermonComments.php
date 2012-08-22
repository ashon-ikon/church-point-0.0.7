<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on May 16, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Comments_SermonComments extends Point_Model_Comments_Abstract
{
	
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	private function __construct()
	{
		 $this->setupComment();
	}
		
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	public function setupComment()
	{
		$this->_table_name 	= 'sermons_comments_table';
		$this->init();	
	}	

	public function getCommentForm()
	{
		
	}	
}