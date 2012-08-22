<?php

class AccountController extends Point_Controller_Action
{

	/**
	 * @var handle to login form
	 */
    protected $_loginForm = null;
    
    private   $_internal_route = false;
    /**
     * @var flag for authentication
     */
    protected	$_loggedIn = false;
    
    protected	$_loginMessages = array();

    private function _prepareLoginDialog()
    {
        
        /* set up the dialog options */
        $floatingDialog = new Point_Model_FloatingDialog();
        $dialogOptions = array(
        						'title' 		=> 'Login',
        						'id'			=> 'login_form',	
        						'linkName'		=> 'Sign in',
        						'class'			=> 'ui-state-default ui-corner-all',
        						
        						/* Button Style for the css */
        						//style="padding: .4em 1em .4em 20px; text-decoration: none; position: relative;"
        						'linkCss'		=> array( 
        													'font-size' 	=> '0.85em',
        													'font-weight'	=> 'bold',
        													'line-height'	=> '1.2em',
        													'text-decoration'=>'none',
        													'color'			=> '#fff',
        													'background'	=> '#88BBD4',
        													'border'		=> '1px solid #88BBE4',
        													'padding'		=> '.4em 1em .4em 20px',
        													'position'		=> 'relative'),
        						/* Dialog settings */
        						'settings'		=> array(
															'width' 	=> 450,
//															'height'	=> 300,
															'draggable'	=> 'false',
															'modal'		=> 'true',
															'resizable'	=> 'false',
															'autoOpen'	=> 'false'
															/*,
															'buttons'	=>	'{"Close":function(){$(this).dialog("close");}}'*/),	
								'others'		=> array(
															'class' => '.signin'));  
															
        return $this->view->userDialog = $floatingDialog->setView($this->view)
        										  		->makeDialog($this->_getForm(), $dialogOptions);
    }

	/**
	 * @method returns a login Form
	 */
	private function _getForm()
	{
		if (null == $this->_loginForm){
			$loginForm	= new Point_Form_Login();
	        $loginForm->setMethod('post')
	        	 	  ->setAction(
	        	 			Zend_Controller_Front::getInstance()->getBaseUrl(). '/' . 
	        	 			$this->_request->getControllerName().'/login')
	        	 	  ->setName(Zend_Controller_Front::getInstance()->getBaseUrl().'account-login');
	       
	       
	       
	       	$retForm = $loginForm->getFormHeadTag();
	       	$retForm .= '<table><tbody>';
	       	$retForm .= $loginForm->email;
	       	$retForm .= $loginForm->password;
	       	//echo '<tr><td width="35%">&nbsp;</td><td><span>', $form->remember, ' Remember me</span></td></tr>';
	       	$retForm .= '<tr><td width="35%">&nbsp;</td><td>'. $loginForm->remember . ' Remember me</td></tr>';
	       	$retForm .= $loginForm->hidden1;
	       	$retForm .= $loginForm->url;
	       	$retForm .= $loginForm->submit;
	       	$retForm .= '</tbody></table>' . $loginForm->formClose();
	       	
	       	$this->loginForm = $retForm;
		}
	       	
	
       	return $this->loginForm;
	}
    
    public function init()
    {
    	parent::init();
        /* Initialize action controller here */
    }

    public function preDispatch()
    {
    	// Check if  user is logged on already...
    	$auth	= Zend_Auth::getInstance();
    	
//    	$this->view->loginIn = $auth->hasIdentity();
    	$this->_loggedIn	 = $auth->hasIdentity();
    
    	/* Check if loging in is neccessary ! */
    	if (!$auth->hasIdentity())
    	{
    		/* If required: force log-in */
    	}
    	
    }
    
    private function _getAuthAdapter()
    {
    	$authAdapter = new Zend_Auth_Adapter_DbTable(
    							Zend_Db_Table::getDefaultAdapter() );
    							
    	$authAdapter->setTableName('users_table')
    				->setIdentityColumn('username')
    				->setCredentialColumn('password');
    	return $authAdapter;
    }

    public function indexAction()
    {
    	// This page SHOULD NOT be assessible
//        $this->_redirect('account/login');
        $this->_forward('profile');
    }

    public function loginAction()
    {
    	/* check if user is logged-in go to home */
    	$thisUser = Point_Model_User::getInstance();
    	
    	if($thisUser->isLoggedIn())
    		$this->_redirect('/');
		
    	$this->view->form = $this->_getForm();
    	
    	/* get content from form */
    	$formRequest 	= $this->getRequest();
    	$form = new Point_Form_Login();
    	if($formRequest->isPost())
    	{
    		$raw_data = $formRequest->getPost();
        	
        	if($form->isValid($raw_data))
        	{
        		
        		$result = $msgs = null;
        		$cleaned_data = $form->getValues();
    				
	    		$email	 		= $cleaned_data['email'];
	    		$password		= $cleaned_data['password'];
	    		$url			= $cleaned_data['url'];
	    		$uri 			= $cleaned_data['url'];
//	    		if (false !== stripos($uri, 'account/login'))
//		    		$uri	= substr($uri, strpos($uri,'?url=')+5);
//		    	$uri		= urldecode($uri);
//	    		echo '<br />'.$uri.'<pre>', print_r($cleaned_data, true), '</pre>'; exit;
	    		
	    		if(!$email && !$password)
	    			return;
	    			
	 			$user	= Point_Model_User::getInstance();
	 			
	 			$result 		= $user->authenticate(array('email' 	=> $email,
	 														'password' 	=> $password),
	 														true /* Store credentials */);
	 			
	 			if ( $result )
	 			{
	 				$this->_loggedIn	= true;
	 				/* Handle remember me stuff */
	 				if($rememberme = $cleaned_data['remember'])
	 				{
	 					$cookie = Point_Object_Cookie::getInstance();
	 					$cookie->set(array('name'=>APP_COOKIE_NAME, 'value' => $result['user_id']));
	 				}
	 				/* Get incoming page and return else go to homepage */
	 				/*====USING hidden input instead =========*/
					$uri 		= $cleaned_data['url'];
					if (false !== stripos($uri, 'account/login'))
						$uri	= substr($uri, strpos($uri,'?url=')+5);
					$uri		= urldecode($uri);	
					if (is_string($uri))
	 				{
		 				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		 				$redirector->gotoUrl($uri)->redirectAndExist();
	 				}
	 				else
	 					$this->_redirect('/');	// Go to root (Home page).
	 				  				
//	 				$uri = Point_Object_Session::getInstance();
//	 				
//	 				if(isset($uri->incoming_uri))
//	 				{		
//	 					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
//	 					
////	 					@file_put_contents('/var/www/php-stuff/tests.log', "\nIncoming Url: '". $uri->incoming_uri ."'", FILE_APPEND);
//	 					
//	 					/* If we are coming from login just go to home page */
//	 					if ($uri->incoming_uri == '/account/login' ||
//	 						$uri->incoming_uri == '/error/nopage' )
//		 					$redirector->gotoUrl('/index/')->redirectAndExist();
//		 						
//			    		$redirector->gotoUrl($uri->incoming_uri)->redirectAndExist();		
//					} else
//	 					$this->_redirect('/');	// Go to root (Home page).
//	 			
	 			}else{ 
		
						$this->_loginMessages [] =  'Failed authetication<br />Invalid username or password combination.';
						
	 
	 			}
    		}
    		else {
    			$this->_loginMessages [] =  'Invalid form submitted';
    			$form->populate($raw_data);
    		}
    		//$this->view->form->email->setValue($raw_data['email']);
    	}
    	
    	if ($form && $messages = $form->getMessages())	 				
    	{
    		$preparedMessages = array();
    		if (is_array($messages))
    		{
	    		foreach($messages as $element => $msg)
				{
		    		if (is_array($msg))
		    		{
			    		foreach($msg as $msg_this)
						{
				    		$preparedMessages[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
						}
		    		}
		    		else
			    		$preparedMessages[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
				}
    		}

    		//$this->view->errors =  $preparedMessages;
	    	$this->_loginMessages = array_merge($this->_loginMessages , $preparedMessages);	
    	}
    	
    	$this->view->messages = $this->_loginMessages;
 		
        $inline			= $this->getRequest()->getParam('inline');
    	       
        if ($inline){ // We are to render the login inline
        	$this->_inlineLogin();
        }else { // render it as a fullpage
        	$this->_normalLogin();
        }
    }
    
    public function registerAction()
    {
    	$config = $helper = $this->view->getHelper('GetAppConfig');	
    	$this->_setTitle('Join the ' . $config->mainName . ' community');
    	
    	$form = new Point_Form_Registration();
    	$form = $form->getForm();
    	$request = $this->getRequest();
    	
        if ($request->isPost())
        {
        	$raw_data = $request->getPost();
        	
        	if($form->isValid($raw_data))
        	{
        		
        		$result = $msgs = null;
        		$cleaned_data = $form->getValues();
	        	$user = Point_Model_User::getInstance();
	        	list($result, $msgs) = $user->addUser($cleaned_data);
	        	if($result)
	        		$this->view->successmsg = 'Congratulations you have just joined our community';
	        	else	
	        	{
	        		
		        	$form->populate($raw_data);
		        	$this->view->form = $form;
		        	$errors = $form->getMessages();
		        	$preparedErrors = array();
		        	if (is_array($errors))
		        	{
			        	foreach($errors as $element => $msg)
						{
				        	if (is_array($msg))
				        	{
					        	foreach($msg as $msg_this)
								{
						        	$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
								}
				        	}
				        	else
					        	$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
						}
		        	}
		        	$preparedErrors [] = $msgs;
		        	$this->view->errors =  $preparedErrors;
	        	}
        	}
        	else
        	{
        		$form->populate($raw_data);
        		$this->view->form = $form;
        		$errors = $form->getMessages();
        		$preparedErrors = array();
        		if (is_array($errors))
        			foreach($errors as $element => $msg)
        			{
        				if (is_array($msg))
        				{
	        				foreach($msg as $msg_this)
							{
								$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
							}
        				}
        				else
        					$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
        			}
        			
        		$this->view->errors =  $preparedErrors;
        	}
        }
        else
    		$this->view->form = $form;
    }

    private function _inlineLogin()
    {
    	
    }

    private function _normalLogin()
    {
		
    }

    public function logoutAction()
    {
    	Point_Model_User::getInstance()->logout();
        $this->_redirect('/');
    }
	
	/**
	 * This is the main method to be called for determining what
	 * will show in the user pane.
	 */
    public function userAction()
    {
        
        $request = $this->getRequest();

        $inline	= $request->getParam('inline');
        
        if($inline){
	        $pix	= Point_Model_Picture::getInstance();
	        //$pix->makeThumbnail('');
	        /*
	         * Check if user is logged-in and act accordingly
	         */
	        $viewRenderer	= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
	       	$viewRenderer->setResponseSegment('userPane');
	       	
	       	$auth	= Zend_Auth::getInstance();
	       	$this->view->username = Point_Model_User::getInstance()->fullname();
	
	       	if ($auth->hasIdentity()){ // We are to render the login profile menu
	       	
	        	$this->_helper->viewRenderer->render('userminipane');
	        
	        } else { // render 'Sign in' page
		    
		        /* create the dialog for user */
		       	$this->_prepareLoginDialog();
		       	$this->_helper->viewRenderer->render('-login');
	        }
	        
        } else { // accidentally called!!!
        	$this->_helper->viewRenderer->setNoRender();
        	$this->_redirect('/');
        }
        
        $this->_helper->viewRenderer->setNoRender(true);
//        $this->_helper->layout->disableLayout();

    }
    
    public function editprofileImageAction()
    {
    	
    }
    
    public function edituserAction()
    {
        $user = Point_Model_User::getInstance();
		$this->view->user = $user->getUserInfo();
	
    	$this->_setTitle('Edit your account');
    	
    	$form = new Point_Form_EditUser();
    	$form = $form->getForm();
    	$request = $this->getRequest();
    	
        if ($request->isPost())
        {
        	$raw_data = $request->getPost();
        	
        	if($form->isValid($raw_data))
        	{
        		
        		$result = $msgs = null;
        		$cleaned_data = $form->getValues();
	        	$user = Point_Model_User::getInstance();
	        	list($result, $msgs) = $user->updateUser($cleaned_data);
	        	if($result)
	        	{
	        		$this->view->user = $user->getUserInfo();
		        	$this->view->form = $form;
		        	if ('successful' !== $msgs)
			        	$this->view->successmsg = 'No changes made';
		        	else
			        	$this->view->successmsg = 'Updated successfully';
	        	}
	        	else	
	        	{
	        		
		        	$form->populate($raw_data);
		        	$this->view->form = $form;
		        	$errors = $form->getMessages();
		        	$preparedErrors = array();
		        	if (is_array($errors))
		        	{
			        	foreach($errors as $element => $msg)
						{
				        	if (is_array($msg))
				        	{
					        	foreach($msg as $msg_this)
								{
						        	$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
								}
				        	}
				        	else
					        	$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
						}
		        	}
		        	$preparedErrors [] = $msgs;
		        	$this->view->errors =  $preparedErrors;
	        	}
        	}
        	else
        	{
        		$form->populate($raw_data);
        		$this->view->form = $form;
        		$errors = $form->getMessages();
        		$preparedErrors = array();
        		if (is_array($errors))
        			foreach($errors as $element => $msg)
        			{
        				if (is_array($msg))
        				{
	        				foreach($msg as $msg_this)
							{
								$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
							}
        				}
        				else
        					$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
        			}
        			
        		$this->view->errors =  $preparedErrors;
        	}
        }
        else
	        $this->view->form = $form;
    }

	public function profileAction()
	{
		
	}

	public function activateAction()
	{
		$user = Point_Model_User::getInstance();
		$this->_setTitle('Account Verification');
		$e_id	= $this->getRequest()->getParam('ut', null);
		$e_code = $this->getRequest()->getParam('u', null);
		
		$id 	= decrypt($e_id		, APP_SALT); 
		$code 	= decrypt($e_code	, APP_SALT);
		$success = $msg = null;
		list($success, $msg) = $user->activate($id, $code);
		
		if ($success)
		{
			$this->view->successmsg = $msg;
		}
		else
		{
			$this->view->errormsg = $msg;
		}
	}
	
	public function forgotpwdAction()
	{
		$form = new Point_Form_ForgotPwd();
		$request = $this->getRequest();
		$this->view->form = $form->getForm();
		$this->view->successmsg = null;
		if ($request->isPost() && !$this->_internal_route)
		{
			$msgs = $result = null;
			$raw_data = $request->getPost();
			
			if ($form->isValid($raw_data))
			{
				$values = $form->getValues();
				$email 	= $values['email'];
				
				$user = Point_Model_User::getInstance();
				
				list($success, $msg) = $user->startPasswordReset($email);
				
				if ($success)
				{
					$this->view->successmsg = $msg;
				}
				else
				{
					$form->populate($raw_data);
					$this->view->form = $form;
					$this->view->errormsg = $msg;
				}
				
				
			}else
			{
				$form->populate($raw_data);
				$this->view->form = $form;
				$errors = $form->getMessages();
				$preparedErrors = array();
				if (is_array($errors))
				{
					foreach($errors as $element => $msg)
					{
						if (is_array($msg))
						{
							foreach($msg as $msg_this)
							{
								$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
							}
						}
						else
							$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
					}
				}
				$preparedErrors [] = $msgs;
				$this->view->errors =  $preparedErrors;
			}
		}
		else if ($request->isGet() && $request->getParam('tu'))
		{
			$raw_data = $request->getParams();
			
			$e_id 		= $request->getParam('tu', null);
			$e_code 	= $request->getParam('t', null);
			
			$id 		= decrypt($e_id	, APP_SALT);
			$code 		= decrypt($e_code	, APP_SALT);
			
			$db 		= new Zend_Db_Table('users_table');
			$where		= $db->getAdapter()->quoteInto('user_id = ? ', (int)$id);
			$ret		= $db->select()->where($where)->query()->fetch();
			
			if ( $ret )
			{
				if($code = $ret['password_reset'])
				{
					
					$this->view->successmsg = '<p>Account Verified</p>';
					$this->_forward('resetpassword',null, null, array('tu'=>$e_id, 't'=>$e_code));
				}
				else
				{
					/* Invalid code */
					$this->view->errormsg = '<strong>Error</strong> Oops! Invalid code given';
				}
				
			}else
			{
				/* Did not find a matching user! */
				$this->view->errormsg = '<strong>Error</strong> Oops! User not within community :(';
			}
			
		}
		
	}
	
	public function resetpasswordAction()
	{
		$form = new Point_Form_ResetPwd();
		$request = $this->getRequest();
		$this->view->successmsg = null;
		
		if ($request->isGet())
		{
			/* Add some hidden stuff during post */
			$c			 = $request->getParam('t');
			$i			 = $request->getParam('tu');
			
			/* Remove old password field */
			if($c) 
				$form->removeElement('oldpassword');
				
			$codeElement = new Zend_Form_Element_Hidden('code');
			$codeElement->setValue($c);
			$hashElement = new Zend_Form_Element_Hidden('hash');
			$hashElement->setValue($i);
			$form->addElements(array($codeElement, $hashElement));
		}
		
		$this->view->form =$form;
		
		if ($request->isPost())
		{
			$msgs = $result = null;
			$raw_data = $request->getPost();
			
			if ($form->isValid($raw_data))
			{
				$values 	= $form->getValues();
				$user_db 	= new Zend_Db_Table('users_table'); 
				
				/* Check if user is logged in else use hash */
				$e_code	= $request->getParam('code', null);
				$e_id	= $request->getParam('hash', null);
				$user 	= Point_Model_User::getInstance();
				if(!$user->isLoggedIn())
				{
					$id = decrypt($e_id, APP_SALT);
				}
				else
				{
					$id = $user->getUserId();
				}
				$where  = $user_db->getAdapter()->quoteInto('user_id = ? ', $id);
				
				if ($request->getParam('oldpassword', null))
				{
					/* Check for match first */
					$oldpassword = $values['oldpassword'];
					
					$result = $user_db->select()->where($where)->query()->fetch();
					if (!empty($result) )
					{		
						if(sha1($oldpassword) == $result['password'] )
						{
							/* Old password is correct */
							
							// Do the change
							if($user->changePassword(sha1($values['password']), $id))
							{
								$this->view->successmsg = 'Password changed successfully';
							}
							else
							{
								$errormsg  = 'Failed to change user\'s password';
								$this->view->errormsg = $errormsg;
							}
							
						}
						else
						{
							$errormsg  = 'Old password does not match';
							$this->view->errormsg = $errormsg;
						}
					}
					else
					{
						$errormsg  = 'Oops! User appears not to be in the community';
						$this->view->errormsg = $errormsg;	
					}
				}
				else
				{
					/* User is not logged in so no old password field */
					$result = $user_db->select()->where($where)->query()->fetch();
					
					if(!empty($result))
					{
						/* Confirm with the given code */
						$code = decrypt($e_code, APP_SALT);
						
						if ($code == $result['password_reset'])
						{
							if ($user->changePassword(sha1($values['password']), $id))
							{
								$this->view->successmsg = 'Password changed successfully';
							}
							else
							{
								$errormsg  = 'Failed to change user\'s password';
								$this->view->errormsg = $errormsg;
							}
						}
						else
						{
							$errormsg  = 'Oops! Invalid reset code given<br /><br /><strong>Try resetting the password again</strong>';
							$this->view->errormsg 	= $errormsg;
							$this->_internal_route 	= true;
							$this->_forward('forgotpwd');
						}
					}
					else
					{
						$errormsg  = 'Oops! User appears not to be in the community';
						$this->view->errormsg = $errormsg;	
					}
					
					
				}
				
			}else
			{
				$form->populate($raw_data);
				$this->view->form = $form;
				$errors = $form->getMessages();
				$preparedErrors = array();
				if (is_array($errors))
				{
					foreach($errors as $element => $msg)
					{
						if (is_array($msg))
						{
							foreach($msg as $msg_this)
							{
								$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
							}
						}
						else
							$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
					}
				}
				$preparedErrors [] = $msgs;
				$this->view->errors =  $preparedErrors;
			}
		}
		else if($request->isGet())
		{
			
		}
	}
	
}
