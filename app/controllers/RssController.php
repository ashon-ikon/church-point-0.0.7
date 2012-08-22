<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Apr 2, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class RssController extends Point_Controller_Action
{
	
	public function preDispatch()
    {
    	$contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('xml', 'xml')->initContext('xml');
        
//    	$this->getResponse()
//                   ->setHeader('Cache-Control',
//                               'no-cache, no-store, max-age=0, must-revalidate, post-check=0, pre-check=0')
//                   ->setHeader('Expires', 'Tue, 14 Aug 1997 10:00:35 GMT');
//
//    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
//    	$ajaxContext->addActionContext('request', 'html')
//    				->initContext();
		// Both layout and view renderer should be disabled
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
        Zend_Layout::getMvcInstance()->disableLayout();
    	
    }
	
    public function init()
    {
    	
    }
    
    public function testAction()
    {
    	
    }
}