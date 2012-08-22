<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on May 16, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
abstract class Point_Model_Comments_Abstract
{
	
	/**
	 * Table name
	 */
	protected	$_table_name 	= null;
	
	protected	$_db_table 		= null;
	
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	/**
	 * Init Stuff
	 */
	public function init()
	{
		$this->setTableName($this->_table_name);
	}
	
	/**
	 * ====================ABSTRACT METHODS==========================
	 * This is where the specific comment's database should be setup.
	 * --------------------------------------------------------------- 
	 */
	abstract function setupComment();
	abstract function getCommentForm();
	
	
	public function getDbTable()
	{
		if (null === $this->_db_table)
		{
			$db	= new Point_Model_DbTable_DbCore($this->_table_name);
			$this->_db_table = $db->getDBTable();
		}
		return $this->_db_table;
	}
	
	public function setTableName($table_name)
	{
		if (is_string($table_name))
			$this->_table_name	= $table_name;
		else
			throw new Exception ('None string name specified');
	}
	
	/**
	 * Posts User Comment
	 */
	public function postComment($comment, $parent_comment_id)
	{
		
	}
	
	public function removeComment($comment_id)
	{
		
	}
	
	public function getCommentsByUser($user_id)
	{
		
	}
	
}