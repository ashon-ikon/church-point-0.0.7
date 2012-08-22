<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: AppConfigController.php
 * 
 * Created by ashon
 * 
 * Created on Jul 27, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */


class AppConfigController extends Point_Controller_Action
{
	protected $_dbAdapter = null;
	
	protected function _getAdapter()
	{
		$authAdapter = new Zend_Auth_Adapter_DbTable(
    							Zend_Db_Table::getDefaultAdapter());
    	return $authAdapter;
	}

    public function init()
    {
        /* Initialize action controller here */
        $this->_getAdapter();
    }

    public function indexAction()
    {
        // action body
    }

    public function headerAction()
    {
    	$config	 = new Application_Model_DbTable_AppConfig();
    	
    	$this->view->app_name = $config->readAllConfig();
        // action body
    }


}



