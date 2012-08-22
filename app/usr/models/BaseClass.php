<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 3, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Model_BaseClass
{
		
	/**
	 * Table name
	 */
	protected	$_table_name 	= null;
	
	protected	$_db_table 		= null;
	
	
	
	/**
	 * @var	int	group id
	 */
	protected	$_content_group_id	= null;
	
	protected	$_content_group_name = null;
	
	
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
	 * Get content group id
	 * 
	 * This retrieves the Sermon content group 
	 */
	public function getContentGroupId($content_group_name = '')
	{
		if (null === $this->_content_group_id)
		{
			$contentGroups	= Point_Model_ContentGroups::getInstance();
			if (null != $content_group_name)
				$this->setContentGroupName($content_group_name);
				
			$this->_content_group_id = $contentGroups->getGroupIdByKeyword($this->_content_group_name);
			
			if (!$this->_content_group_id)
				throw new Exception('Unable to get content group id for ' . $this->_content_group_name);
		}
		
		return $this->_content_group_id;
	}
	
	public function setContentGroupName( $content_group_name = null)
	{
		if (null != $content_group_name && is_string($content_group_name))
			$this->_content_group_name = $content_group_name;
		else
			throw new Exception('Content group name must be string.');
	}
	
	/* Helper functions to facilitate setting and getting of known members */
	public function __set($name, $value)
	{	$var = '_' . $name;
		if (array_key_exists($var, get_class_vars(get_class($this)))){
			$this->$var	= $value;
		}
	}
	
	public function __get($name)
	{
		$var = '_' . $name;
		if (array_key_exists($var, get_class_vars(get_class($this)))){
			return $this->$var;
		}else {
			// Throw an exception...
			throw new Exception ('Invalid member ('. (string)$name .')' );
		}	
	}

}
