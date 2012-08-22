<?php

class AboutController extends Point_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	// SEO stuff
        $this->view->headMeta()->setProperty('og:title', 'About ' . APP_CHURCH_NAME . ' - ChurchPoint');
		$this->view->headMeta()->setProperty('og:type', 'other');
		$this->view->headMeta()->setProperty('og:description', APP_CHURCH_DESCRIPTION);
		$this->view->headMeta()->appendName('robots', 'index, follow');
    	$this->_helper->layout()->page_description 	= 'About: ' . APP_CHURCH_DESCRIPTION;
    	$this->_helper->layout()->page_keywords 	= 'Methodist Christian Church, Chinese, English, African';
    }

    public function workersAction()
    {
        // action body
    }

    public function locationAction()
    {
        // action body
    }

    public function privacyAction()
    {
        // action body
    }

    public function policyAction()
    {
        // action body
    }


}









