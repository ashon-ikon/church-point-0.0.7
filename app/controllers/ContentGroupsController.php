<?php

class ContentGroupsController extends Point_Controller_Action
{
	/**
	 * @var	int	group id
	 */
	protected	$_content_group_id	= null;

	/**
	 * @var array errorMsgs
	 */	
	protected	$_errorMsgs			= array();
	
	
	public function preDispatch()
	{
		/* Check if user is privileged to view page */
    	$user				= Point_Model_User::getInstance();
    	$contentGroups		= Point_Model_ContentGroups::getInstance();
        
        $userAccess			= $contentGroups->getMembership($user->getUserId(),$this->_content_group_id);
       
        if( !Point_Model_ContentGroups::GROUP_MEMBER === $userAccess ||
         	!Point_Model_ContentGroups::GROUP_ADMIN 	=== $userAccess  )
        {
        	/* User is not previledged*/
	        $this->_redirect('error/noaccess');
	         
        }
             
	}
    public function init()
    {
        /* Initialize action controller here */
	    $contentGroup	= Point_Model_ContentGroups::getInstance();
		
		$this->_content_group_id	= $contentGroup->getGroupIdByKeyword('Users');
		
		if (!is_numeric($this->_content_group_id))
			throw new Exception('No valid group id found for User Managers');	 
	
    }

    public function indexAction()
    {
    	/* Check if user is privileged to view page */
    	$user				= Point_Model_User::getInstance();
    	$contentGroups		= Point_Model_ContentGroups::getInstance();
    	
    	/* Get all groups and show them */
    	$allgroups				= $contentGroups->getAllGroups();
    	
    	/* Get members count for each group */
    	foreach($allgroups as &$group)
    	{
    		if ($group_count 	= $contentGroups->getMembers($group['group_id']))
    			$group['group_count']	= count($group_count);
    		else
	    		$group['group_count']	= 0;
    	}
    	
    	
    	/* Show all the groups and allow user to choose one */
    	$this->view->all_groups	= $allgroups;
    	
    	
    	if (!empty($this->_errorMsgs))
    		$this->view->errormsgs	= $this->_errorMsgs; 
    	
        
    }
    
    public function viewgroupAction()
    {
    	/* Get the incoming group id else move to index */
    	$request		= $this->getRequest();
    	
    	if (!$group_id 	= $request->getParam('g_id', null))
    	{
    		$this->_errorMsgs[]	= 'Please select group to view first';
    		
    		/* Go back to the selection page */
	        $this->_redirect('content-groups/');
    	}
    	
    	/* Show group info */
    	$this->view->form	= null;
    	
    }
    
    public function addmemberAction()
    {
    	
    }

	public function addcontentGroupAction()
	{
		
	}

}

