<?php

class RrPageController extends Point_Controller_Action
{

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
    	}
    } 

    public function indexAction()
    {
/*    	$bootstrap 	= $this->getInvokeArg('bootstrap');
        $view		= $bootstrap->getResource('view');
//TODO: $view->headTitle('INDEX');
        $contents	= new Application_Model_ContentMapper();
        $this->view->contents = $contents->findAll();
        */
        
//        $data	  = array('cache_dir'	=>	'Value',
//        				  'tpl_dir' 	=>	'here');
//        $testRTPL = new RainTpl_View_RainTpl($data);
//        
//        $layoutView	= new Point_View_Layout();
//        $layoutView->setScriptPath(APPLICATION_PATH . '/layouts/scripts/layout');
//        
//        $layout = Zend_Layout::getMvcInstance();
////        $layout->setView($layoutView);
      
       
//        $viewRenderer	= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
//        $viewRenderer->setNoRender();
//        
//        $view	= new Zend_View();
////        echo $this->getViewScript('login');
//       	$view->addScriptPath(APPLICATION_PATH . '/views/scripts/rr-page'/*$this->view->getViewScript('login')*/);
//    	echo $view->render('login.phtml');
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
/*
		$bootstrap 	= $this->getInvokeArg('bootstrap');
		$configs	= $bootstrap->getOption('raintpl');
		$options	= array('cache_dir' => $configs['cache_dir'],
							'tpl_dir'	=> $configs['view']['tpl_dir'],
							'tpl_ext'	=> $configs['tpl_ext']);

		$view	= new RainTpl_View_RainTpl/*Point_View_Template_RainTpl* /($options);
		$viewRenderer	= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setView($view);
        $viewRenderer->setViewSuffix('tpl');
        //Update the helper stack
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        */        
//        $view	= new RainTpl_View_RainTpl/*Point_View_Template_RainTpl*/($data);
//        echo $this->getViewScript('login');
//       	$view->addScriptPath(APPLICATION_PATH . '../public/templates'/*$this->view->getViewScript('login')*/);
//        $view->assign('testa','Happy Me');
//        echo $view->render('test');
    }


}



