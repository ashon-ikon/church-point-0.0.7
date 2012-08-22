<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Sep 30, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Application_Model_DbTable_Menus extends Point_Model_DbTable_DbCore
{

    protected 	$_name 		= 'pages_table';
    protected 	$_primary	= 'page_id';
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
    		$this->setDBTable(get_class($this)/*'Application_Model_DbTable_Menus'*/);
    	}
    	
    	return $this->_dbTable;
    }
    
    
    /**
     * @method readPageById
     * 
     * @param $Page_id
     * 
     * @return array of Page info
     *  
     */
     public function getPageById($page_id)
     {
     	$page = null;
     	
     	$result = 		$this->getDBTable()->fetchRow(
     											$this->getDBTable()->select()->where('page_id = "' . $page_id . '"'));
     	if (!$result){
     		throw new Exception('Could not find Menu ('.$page_id.')');
     	}
     	
     	return $page;
     }
     
     /**
     * @method readAll
     * 
     * @param null
     * 
     * @return Array PagesArray
     *  
     */
     public function readAll()
     {
     	
     	$results = 	$this->getDBTable()->fetchAll(
     							$this  ->getDBTable()
     								   ->select());
     	if (!$results){
     		throw new Exception('Could not read all pages');
     	}
     	$pages = array();
		foreach ($results as $result){
     		$pages[] = $result;//->toArray();
		}
     	
     	return $pages;
     }
     
     /**
     * @method updatePage
     * 
     * @param $page_id
     * 
     * @return mixed result
     *  
     */
     public function updatePage($page_id)
     {
     	
     }
     
     /**
     * @method delPage
     * 
     * @param $page_id
     * 
     * @return null
     *  
     */
     public function delPage($page_id)
     {

     }
     
     /**
     * @method savePage
     * 
     * @param $page_id
     * 
     * @return mixed result
     *  
     */
     public function saveConfig($config)
	{
		
	}
}

