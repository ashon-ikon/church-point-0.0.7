<?php

class RrPageController extends Point_Controller_Action
{
	/**
	 * handle to user info
	 */
	protected	$_user = null;
	
    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function preDispatch()
    {
    	$auth	= Zend_Auth::getInstance();
    	
    	if (!$auth->hasIdentity())
    	{
    		
    		$this->view-> message = 'You are not logged in';	
    	}else{
    		$this->_user	= $auth->getStorage()->read();
    	}
    } 

    public function indexAction()
    {
    	
    }

    public function loginAction()
    {
    	$test = new Zend_Config_Ini(APPLICATION_PATH . '/configs/' . 'extras.ini', 'production');
    	$this->view->test = $test->toArray();
        // action body
        
        $form	= new Application_Form_Login();
        $this->view->form = $form;
        $buff = null;        
    }
    
    private function _getAuthAdapter()
    {
    	$authAdapter = new Zend_Auth_Adapter_DbTable(
    							Zend_Db_Table::getDefaultAdapter() );
    	$authAdapter->setTableName('users')
    				->setIdentityColumn('username')
    				->setCredentitalColumn('password');
    	return $authAdapter;
    }
    
    public function testAction()
    {
    	$user	= new Point_Model_User();
    	$user->getUserById(1);
    	echo'<pre>';
    	echo print_r($user->access_level, true), 'one', 'two';
    	echo '</pre>';
		if (isset($this->_user))
		{
			echo print_r($this->_user, true);
		}
    }


}



