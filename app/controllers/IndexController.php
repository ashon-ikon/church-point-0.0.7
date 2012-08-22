<?php

class IndexController extends Point_Controller_Action
{
	protected	$_template_content;
	
    public function preDispatch()
    {
    	parent::preDispatch();
    	
    	if (method_exists(get_class($this), $this->getRequest()->getParam('action').'Action'))
    	{
//		
//	    	$this->_page_info = $this->_page->retrievePageInfo( $this->getRequest()->getParams());
//	    	$this->view->admin_content = $this->_page->getPageAdminButton($this->_page_info, $this->getRequest());
//	    	
//	    	$this->_template_content = $this->_page->getPage( $this->getRequest()->getParams(), false );
    	}else
    	{
    		$this->_redirect('error/nopage');
    	}
    	
    }

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        // action body
        $this->view->template_content = $this->_template_content;
		$this->view->headMeta()->setProperty('og:title', APP_CHURCH_NAME . ' home page');
		$this->view->headMeta()->setProperty('og:type', 'other');
		$this->view->headMeta()->setProperty('og:description', APP_CHURCH_DESCRIPTION);
		$this->view->headMeta()->appendName('robots', 'index, follow');
    	$this->_helper->layout()->page_keywords = '';
    	
    	/*Let's  get all the agents for 'Home' page */
    	$pageObj	= Point_Model_Page::getInstance();
    	
    	
    	/* render page */
		$this->view->template_content = $pageObj->getContents('index/index');
    }

    public function aboutAction()
    {
		$this->_redirect('about/index');		
    }

    public function noscriptAction()
    {
		$layout		= Zend_Layout::getMvcInstance();
		$layout->disableLayout();
    }

    public function vistaAction()
    {
	
    }

    public function bingoAction()
    {		
    
	    $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
	    if ($options = $bootstrap->getOption('app'))
	    {
//	    	var_dump ($options->app);	
	    }

		$news_event_obj			= Point_Model_NewsEvents::getInstance();
		
		$news_event_result		= $news_event_obj->getNewsEventsByPeriod(array(
									'view' => Point_Model_NewsEvents::VIEW_MONTH,
									'time' => strtotime('2012-04-26 22:17:08')
								));
		pr($news_event_result);

	    /**
	     * =====================
	     * POST to SOCIAL NETWORK
	     * e.g facebook
	     */
//	    $social_agent	= Point_Model_Robots_SocialUpdate::getInstance();
	    
    }
    
}
