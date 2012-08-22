<?php

/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: AppConfigMapper.php
 * 
 * Created by ashon
 * 
 * Created on Jul 27, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */

class Application_Model_AppConfigMapper
{
	protected	$_dbTable;
	
	/**
     * @method setDBTable mixed Allocates a valid db
     * 
     * @param mixed dbTable
     */
    function setDBTable($dbTable)
    {
    	if (is_string($dbTable))
    	{
    		$dbTable = new $dbTable();
    	}
    	
    	if (!$dbTable instanceof Zend_Db_Table_Abstract)
    	{
    		throw new Exception ('Invalid table data gateway provided');
    	}
    	
    	$this->_dbTable = $dbTable;
    	return $this;
    }
    
    /**
     * @method getDBTable mixed db
     * 
     * @param null
     */
    function getDBTable()
    {
    	if (null === $this->_dbTable)
    	{
    		$this->setDBTable('Application_Model_DbTable_Content');
    	}
    	
    	return $this->_dbTable;
    }
    
    public function save()
    {
    	
    }
    
    /**
     * @method mixed Gets information from database
     */
    public function find($c_id, Application_Model_Content $content)
    {
    	$result = $this->getDBTable()->find($c_id);
    	if (0 == count($result))
    	{
    		return;
    	}
    	$row 		= $result->current();
    	$content->setId($row->c_id)
    			->setContentKeyId($row->content_key_id)
    			->setContent($row->content);
    }
	
	/**
	 * @method mixed Gets all the records within the database
	 */
	 public function findAll()
	 {
	 	$resultSet 	= $this->getDBTable()->fetchAll();
	 	$entries[] 	= array();
	 	
	 	foreach ($resultSet as $row)
	 	{
	 		$entry = new Application_Model_Content();
            $entry->setId($row->c_id)
		 		  ->setContentKeyId($row->content_key_id)
		 		  ->setContent($row->content);
		 	$entries[]	=	$entry; 
		}
		return array_filter($entries);
	 }
	
}

