<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	User controller: ChurchPoint
 * Created by ashon on Oct 09, 2011
 * (c) 2011 Copyright
 * -------------------------------------------
 */
class UserController extends Point_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_forward('profile');
    }

    public function registerAction()
    {
    	//Set the H1 tags for the page.
        $this->view->title	= "";
    }

    public function editAction()
    {
        $user = Point_Model_User::getInstance();
		$this->view->user = $user->getUserInfo();
    }

    public function editimageAction()
    {
        // action body
    }

    public function removeAction()
    {
        // action body
    }

    public function profileAction()
    {
    	$user_obj 		= Point_Model_User::getInstance();
    	
    	$request 	= $this->getRequest();
   
    	
    	/* Ensure we have a valid user ID */
    	$id 	= $request->getParam('id', $user_obj->getUserId() );
    	
    	
    	if( $user_info 			= $user_obj->getUserInfo($id))
    	{
	    	
	    	//Set the H1 tags for the page.
	    	$user_fullname 		= $user_info['user_lname'] . ', ' . $user_info['user_fname'];
	    	
	    	/* Extract all groups */
	    	$cGroups_obj				= Point_Model_ContentGroups::getInstance();
	    	$user_cGroups				= $cGroups_obj->getUserGroups($id);
	    	$user_info['user_cgroups']	= $user_cGroups;
	    	
	    	/* Make link for user image */
	    	if ($id == $user_obj->getUserId())
	    	{
	    		$picsLink		= $this->_getBaseUrl() . '/' . $request->getControllerName() . '/editimage/?u_id=' . urlencode($id);
	    		
	    		$this->view->ownPixLink	= $picsLink;
	    	}
	    	$this->view->user 	= $user_info;
	    	
	    	$this->_setTitle($user_fullname . '\'s profile');
    	}
    }

    public function editpasswordAction()
    {
		
    }

    public function viewallAction()
    {
        $user_obj 			= Point_Model_User::getInstance();
        $users				= $user_obj->getAllUsers();
		/**
		 * Compute user profile url
		 */
		$base_url			= $this->_getBaseUrl() . '/';
		
		foreach($users as &$user)
		{
				$user['user_profile_url'] = $base_url . 'user/profile/?id=' . htmlentities($user['user_id']);
		}
		$this->view->users 	= $users;
		
		$this->_setTitle('All members');
    }
    
    public function manageuserAction()
    {
    	
    	
    }
    
    public function managecontentgroupsAction()
    {
    	
    }

}











