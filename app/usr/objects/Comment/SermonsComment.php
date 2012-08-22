<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Apr 7, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Object_Comment_SermonsComment
{
	/**
	 * Table name
	 */
	protected	$_table_name 	= 'sermons_comments_table';
	
	protected	$_db_table 		= null;
	
	/**
	 * @var	int	group id
	 */
	protected	$_content_group_id	= null;
	
	/**
	 * @var	string	group keyward
	 */
	protected	$_content_group_keyword	= 'Sermon Comment';
	
	
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	private function __construct()
	{
		$contentGroup	= Point_Model_ContentGroups::getInstance();
		
		$this->_content_group_id	= $contentGroup->getGroupIdByKeyword($this->_content_group_keyword);
		
		if (!is_numeric($this->_content_group_id))
			throw new Exception('No valid group id found for "<i>'. $this->_content_group_keyword . '</i>"');	 
	}
		
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	public function getDbTable()
	{
		if (null === $this->_db_table)
		{
			$db	= new Point_Model_DbTable_DbCore($this->_table_name);
			$this->_db_table = $db->getDBTable();
		}
		return $this->_db_table;
	}
}