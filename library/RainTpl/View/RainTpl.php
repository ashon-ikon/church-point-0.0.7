<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: RainTpl.php
 * 
 * Created by ashon
 * 
 * Created on Aug 2, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
/**
 * Include the RainTpl Base Class
 * (c) Rain Team
 */
require_once ('RainTpl/RainTplClass.php');

class RainTpl_View_RainTpl extends Zend_View_Abstract{
	
	/**
	 * Rain TPL Object 
	 */
	 protected 	$_raintpl;
	 /**
	  * Template directory (path)
	  */
	 //protected 	$tpl_dir;
	 
	 /**
	  * Cache Directory
	  */
	  //static	$cache_dir;
	 
	 /**
	  *  Do custom RainTPL thingy
	  */
	function __construct($data) {
	    parent::__construct($data);
	
	    $this->_raintpl = new RainTPL();
/*	    
	    RainTPL::$tpl_dir = $data['tpl_dir'];
	    RainTPL::$cache_dir = $data['cache_dir'];
*/
	    if (count($data)>0){
	    	foreach ($data as $key => $val){
	    		RainTPL::$$key = $val;
	    	}
	    }
/*	    if (array_key_exists('tpl_ext', $data)){
	    	RainTPL::$tpl_ext = $data['tpl_ext'];
		}
*/
	}
	
	public function getEngine() {
		return $this->_raintpl;
	}
	
	public function __set($key, $val) {
	    $this->_raintpl->assign($key, $val);
	}
	
	public function __get($key) {
	      return isset( $this->_raintpl->$var[$key] ) ? $this->_raintpl->$var[$key] : null;
	}
	
	public function __isset($key) {
	    return $this->_raintpl->get_template_vars($key) != null;
	}
	
	public function __unset($key) {
	    $this->_raintpl->clear_assign($key);
	}
		
	public function assign($spec, $value=null) {
	    if (is_array($spec)) {
	    	$this->_raintpl->assign($spec);
	    	return;
		}
		$this->_raintpl->assign($spec, $value);
	}
	
	public function clearVars() {
	    $this->_raintpl->$var=array();
	}
	
	public function render($name,$return_string=false) {
		if( $ext = strrchr($name, '.') )
	    	$name = substr($name, 0, -strlen($ext));  
	    return $this->_raintpl->draw($name,$return_string);
	}
 
	public function _run($nothing = null)
	{
		
	}		
}
