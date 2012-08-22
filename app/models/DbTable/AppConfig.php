<?php

class Application_Model_DbTable_AppConfig extends Point_Model_DbTable_DbCore
{

    protected 	$_name 		= 'app_config';
    protected 	$_primary	= 'id';
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
    		$this->setDBTable('Application_Model_DbTable_AppConfig');
    	}
    	
    	return $this->_dbTable;
    }
    
    
    /**
     * @method readConfig
     * 
     * @param $config
     * 
     * @return string ConfigValue
     *  
     */
     public function readConfig($config)
     {
     	$value = null;
     	
     	$result = 		$this->getDBTable()
     						 ->fetchRow(
     							$this->getDBTable()
     								 ->select()->where('configName = "' . $config . '"'));
     	if (!$result){
     		throw new Exception('Could not find configuration setting');
     	}
     	$value = $result['settings'];
     	
     	return $value;
     }
     
     /**
     * @method readAllConfig
     * 
     * @param null
     * 
     * @return Array ConfigValues
     *  
     */
     public function readAllConfig()
     {
     	
     	$results = 	$this->getDBTable()->fetchAll(
     							$this  ->getDBTable()
     								   ->select());
     	if (!$results){
     		throw new Exception('Could not find configuration setting');
     	}
     	$values = array();
		foreach ($results as $result){
     		$values[] = $result->toArray();
		}
     	
     	return $values;
     }
     
     /**
     * @method updateContent
     * 
     * @param $config
     * 
     * @return mixed result
     *  
     */
     public function updateConfig($config)
     {
     	
     }
     
     /**
     * @method delConfig
     * 
     * @param $config
     * 
     * @return null
     *  
     */
     public function delConfig($config)
     {

     }
     
     /**
     * @method saveConfig
     * 
     * @param $config
     * 
     * @return mixed result
     *  
     */
     public function saveConfig($config)
	{
		
	}
}

