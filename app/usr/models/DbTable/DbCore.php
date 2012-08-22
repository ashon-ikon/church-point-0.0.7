<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Sep 18, 2011
 * (c) 2011 Copyright
 * -------------------------------------------
 */
 
/**
 *  THIS CLASS IS INTENDED TO EXTEND THE FEATURES OF memCache someday...
 */
class Point_Model_DbTable_DbCore extends Zend_Db_Table_Abstract {
	/**
	 * DB Cache
	 */
	protected 	$_cache		= null;
	
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	/**
	 * @var _name
	 */
	protected 	$_name;
	
	/**
	 * @var Zend_Db_Table_Abstract Zend_Table
	 */
	protected	$_dbTable;
	/**
	 * @param string $tableName Name of table
	 */
	public function __construct($tableName, $options = null)
	{
		/* Store table name */
		 $this->setTableName($tableName);
		 
		 // Use default adapter
		 if (empty($options))
		 	$options = Zend_Db_Table::getDefaultAdapter();
		 	
		 parent::__construct($options);
	}
	
	protected function setupCache ($options)
	{
		if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        
        if (!is_array($options)) {
            throw new Exception('Invalid cache options; must be array or Zend_Config object');
        }

        if (array('frontend', 'backend', 'frontendOptions', 'backendOptions') != array_keys($options)) {
            throw new Exception('Invalid cache options provided');
        }

        $this->_cache = Zend_Cache::factory(
					            $options['frontend'],
					            $options['backend'],
					            $options['frontendOptions'],
					            $options['backendOptions']
				        			);
		return $this;
	}
	
	public function init($tableName = null)
	{
		
	
	} 
	
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	        
	    return self::$_instance;
	}
	
	/**
	 * @method: setTableName
	 * @param:	String tablename
	 */
	public function setTableName($tableName)
	{
		if(!empty($tableName) && is_string($tableName))
			$this->_name = $tableName;
		else
			throw new Exception('There was a problem setting the Table Name <i>"'. $tableName .'"</i>');
		return $this;
	}
	
	/**
	 * @method: getTableName
	 * @return:	String tablename
	 */
	public function getTableName()
	{
		return $this->_name;
	}
	/**
     * @method setDBTable mixed Allocates a valid db
     * 
     * @param mixed dbTable
     */
    function setDBTable($dbTable)
    {
    	$tb = $dbTable;
    	if (is_string($dbTable))
    	{
    		$dbTable = new $this($this->getTableName());
    	}
    	
    	if (!$dbTable instanceof Zend_Db_Table_Abstract)
    	{
    		throw new Zend_Exception ('Invalid table provided ('. (string)$tb .')' );
    	}
    	
    	$this->_dbTable = $dbTable;
    	return $this;
    }
    
    /**
     * @method getDBTable mixed db
     * 
     * @param null
     */
    public function getDBTable()
    {
    	if (null === $this->_dbTable)
    	{
    		$this->setDBTable(get_class($this));
    	}
    	return $this->_dbTable;
    }
    
    
    /**
     * @method readById
     * 
     * @param $Page_id
     * 
     * @return array of Page info
     *  
     */
     public function getById($field, $id)
     {     	
     	$result = 		$this->getDBTable()->fetchRow(
     					$this->getDBTable()->select()->where( '`'.$field. '` = ?', (int)$id ));
     	if (!$result){
     		throw new Exception('Could not find item (ID: '.$id. ' in FIELD: '. $field .')');
     	}
     	
     	return $result;
     }
     
     /**
     * @method readAll
     * 
     * @param null
     * 
     * @return Array 
     *  
     */
     public function readAll($where = null, $cond = null)
     {
     	$select	 = (empty($where)? 
     				$this->getDBTable()->select() : 
     				$this->getDBTable()->select()->where($where, $cond));
     				
		$results = 	$this->getDBTable()->fetchAll($select);
     	if (!$results){
     		throw new Exception('Could not read all items from ('.$this->_name .')');
     	}
     	
     	return $results;
     }
     
     /**
     * @method update
     * 
     * @param $id
     * 
     * @return mixed result
     *  
     */
     public function ___update($field, $id)
     {
     	
     }
     
     /**
     * @method delete
     * 
     * @param $id
     * 
     * @return null
     *  
     */
     public function ___delete($field, $id)
     {

     }
     
     /**
     * @method add
     * 
     * @param Record field $field
     * @param ID $id
     * @return mixed result
     *  
     */
     public function add($field, $id)
	{
		
	}
}