<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: content.php
 * 
 * Created by ashon
 * 
 * Created on Jul 24, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
class Application_Model_DbTable_Content extends Point_Model_DbTable_DbCore
{

	/**
	 * Table name
	 */
    protected $_name 	= 'contents_table';
    /**
     * Primary key
     */
    protected $_primary	= 'content_id';
    
    /**
     * @method readContent
     * 
     * @param $content
     * 
     * @return string ContentValue
     *  
     */
     public function readContent($content)
     {
     	$value = null;
     	$this->fetchRow($content);
     	
     	return $value;
     }
     
     /**
     * @method updateContent
     * 
     * @param $content
     * 
     * @return mixed result
     *  
     */
     public function updateContent($content)
     {
     	
     }
     
     /**
     * @method delContent
     * 
     * @param $content
     * 
     * @return null
     *  
     */
     public function delContent($content)
     {

     }
     
     /**
     * @method saveContent
     * 
     * @param $content
     * 
     * @return mixed result
     *  
     */
     public function saveContent($content)
	{
		
	}
}

