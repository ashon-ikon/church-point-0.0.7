<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 3, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Model_User extends	Point_Model_BaseClass
{
	/**
	 * @var Zend_Db_Table_Abstract Database Table
	 */
	protected	$_userAdapter		= null;
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	private	static	$_loggedIn	= false;
	
	protected	$_tableName			= 'users_table';
	
	//	user_id	username	password	email	user_role	active	reg_date
	
	const		GUEST				= 'guest';
	const		MEMBER				= 'member';
	const		ADMIN				= 'admin';
	const		SUPERADMIN			= 'superadmin';
	
	/**
	 * Group membership types
	 */	
	const		GROUP_GUEST					= 'guest';
	const		GROUP_MEMBER				= 'member';
	const		GROUP_MODERATOR				= 'moderator';
	const		GROUP_ADMIN					= 'admin';
	
	protected	$_user_id			= null;
	protected	$_username			= 'guest';
	protected	$_email				= null;
	protected	$_user_role			= null;
	protected	$_user_fname		= null;
	protected	$_user_lname		= null;
	protected	$_user_role_num		= null;
	protected	$_active			= null;
	protected	$_reg_date			= null;
	
	protected	$_menuParentClass	= null;
	protected	$_menuChildClass	= null;
	
	/**
	 * Set if user was not logged in from terminal
	 * i.e. idenity was from registry === true
	 * 		else identy from authentication===true
	 */
	public		$realAuthentication	= false;
		
	private function __construct()
	{
		 
	}
	
	public function init()
	{
		
	}
		
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	private	function _getTableName()
	{
		return $this->_tableName;
	}
	
	public function getUserDbTable()
	{
		if (null === $this->_userAdapter)
		{
			$db	= new Point_Model_DbTable_DbCore($this->_getTableName());
			$this->_userAdapter = $db->getDBTable();
		}
		return $this->_userAdapter;
	}
	
	private function _getAuthAdapter()
    {
    	$authAdapter = new Zend_Auth_Adapter_DbTable(
    							Zend_Db_Table::getDefaultAdapter() );
    							
    	$authAdapter->setTableName($this->_getTableName())
    				->setIdentityColumn('email')
    				->setCredentialColumn('password');
    	return $authAdapter;
    }
    
    /**
     * returns True if all needed info is available
     */
    protected function _infoComplete($user_info, $pwdOptional = false)
	{
		/**
    	 * Ensure we have all the required fields...
    	 */
    	$noerror = true;
    	$noerror &= array_key_exists('firstname',$user_info);
    	$noerror &= array_key_exists('lastname',$user_info);
    	$noerror &= array_key_exists('gender',$user_info);
    	$noerror &= array_key_exists('email',$user_info);
//    	$noerror &= array_key_exists('mobile',$user_info); /* Optional */
    	$noerror &= array_key_exists('day',$user_info);
    	$noerror &= array_key_exists('month',$user_info);
    	$noerror &= array_key_exists('year',$user_info);
    	$noerror &= array_key_exists('address1',$user_info);
//    	$noerror &= array_key_exists('address2',$user_info); /* Optional */
    	$noerror &= array_key_exists('town',$user_info);
//    	$noerror &= array_key_exists('zip',$user_info); 	 /* Optional */
		if (!$pwdOptional)
    		$noerror &= array_key_exists('password',$user_info);
    	$noerror &= array_key_exists('state',$user_info);
    	$noerror &= array_key_exists('country',$user_info);
    	
    	return $noerror;	
	}
    
    /**
     * Register a new user 
     * 
     * @param Array User info
     */
    public function addUser($user_info)
    {
    	 
    	if(!$noerror = $this->_infoComplete($user_info))
    	{
    		return array ((bool)$noerror, 'Incomplete User info');
    	}

    	
    	if(!array_key_exists('user_role', $user_info))
			$user_info['user_role'] = self::MEMBER;
		
		if(!array_key_exists('address2', $user_info))
			$user_info['address2'] = 'N/A';
		
		if(!array_key_exists('zip', $user_info))
			$user_info['zip'] = 'N/A';		
		
		if(!array_key_exists('mobile', $user_info))
			$user_info['mobile'] = 'N/A';
		
		/**
		 * ---------------
		 * Insert the basic info
		 */
		/* Check if email exists already */
		$check = $this->getUserDbTable()->select()->where('email = ?', $user_info['email'], 'VARCHAR')->query()->fetch();
		
		if ($check)
		{
			return array (false, 'User already exists!');
		}
		/* user table */		
    	
    	$reg_date = date( 'Y-m-d H:i:s', time());
    	
    	$activation_token = md5($reg_date);
    	
    	//	user_id	username	password	email	user_role	active	reg_date
    	
    	$userAdapter 	= $this->getUserDbTable();
    	
    	$newUser   		= array(
    							'email' 		=> $user_info['email'],
    							'user_fname' 	=> $user_info['firstname'],
    							'user_lname' 	=> $user_info['lastname'],
    							'user_role' 	=> $user_info['user_role'],
    							'password'		=> sha1($user_info['password']),
    							'active'		=> $activation_token,
    							'reg_date'		=> new Zend_Db_Expr('NOW()')
    							);
    	
       	if(($insert_id = $userAdapter->insert($newUser))===false)
    	{
    		return array (false, 'Failed to add new info');
    	}
    	
    	$user_id = $insert_id;
    	
    	/**
   	  	 * ==================================
    	 * 	IMAGE FOR USE PROFILE
    	 * ++++++++++++++++++++++++++++++++++
    	 */
    	$picture_obj		= Point_Model_Picture::getInstance();
    	
    	$user_album_name	= $picture_obj->addAlbum($user_info['firstname']. rand(4,6), $user_info['firstname'].'\'s profile ablum ');
    	
    	/**
    	 * ------------
    	 * Insert other info
    	 */
    	 $dob = date( 'Y-m-d H:i:s', strtotime($user_info['day'] . '-' . $user_info['month'] . '-' . $user_info['year'] ) );
    	 
    	 //user_info_Id`, `user_country_id`, `user_state_id`, `user_address`, `user_address2`, `user_zip`, `user_town`, `user_mobile`
    	 $db = new Zend_Db_Table('users_info_table');
    	 
    	 $newUserInfo   = array('user_id' 		=> $user_id,
    	 						'user_gender'	=> ($user_info['gender'] == 1 ? 'male' : 'female'), 
    	 						'user_address'	=> $user_info['address1'], 
    	 						'user_address2'	=> $user_info['address2'], 
    	 						'user_date_of_birth'		=> $dob, 
    	 						'user_profile_image_album'	=> $user_album_name, 
    	 						'user_mobile'	=> $user_info['mobile'],     	
    	 						'user_zip'		=> $user_info['zip'],     	
    	 						'user_town'		=> $user_info['town'],    
    	 						'user_state_id'	=> $user_info['state'],     	
    	 						'user_country_id'	=> $user_info['country']
    	 						);     	
    	 
    	 if($db->insert($newUserInfo) === false )
    	 {
    	 	/* remove user */
    	 	$user_db	= $this->getUserDbTable();
    	 	$where 		= $user_db->getAdapter()->quoteInto('user_id = ?', $user_id);
    	 	$user_db->delete($where);
    	 	
    		return array (false, 'Failed to add new user info');
    	 }
    	 
    	 
    	 
    	 
    	 /**
    	  * Add new user to public group
    	  * group_id	user_id	group_access	last_seen	reg_date
    	  */
    	 $data 	= array('group_id' 		=> 1,
    	 				'user_id'  		=> $insert_id,
    	 				'group_access'	=> self::GROUP_MEMBER,
    	 				'last_seen'		=> new Zend_Db_Expr('NOW()'),
    	 				'reg_date'		=> new Zend_Db_Expr('NOW()')
    	 				 ); 
    	 $groups_members_table = new Zend_Db_Table('groups_members_table');
    	 
    	 $groups_members_table->insert($data);
    	 
    	 /**
    	  * Log new user in 
    	  */
    	 $this->authenticate(array('email'=>$user_info['email'], 'password'=>$user_info['password']), true);
    	 
    	 /**
    	  * Add all basic groups a member user must belong to
    	  * ================================================= 
    	  */
    	 $userContentGroups 	= Point_Model_ContentGroups::getInstance();
    	 
    	 //public function addMember($user_id, $group_id, $group_access = 'member')
    	 $basic_groups		 	= array(
    	 								array('name' => 'Images Editors Group'	,	'id' => 4),
    	 								array('name' => 'Images Editors Group'	,	'id' => 4),
    	 								);
    	 
    	 /**
    	  * ---------------------------------
    	  * Send email to user for activation
    	  * --------------------------------- 
    	  */
    	 $configs 		= Zend_Registry::get('config');
    	 $mailOptions 	= $configs->app->mail;
    	 $message 		= $subject = null;
    	 $message 		= $this->_makeWelcomeMessage($activation_token);
    	 $subject 		= 'Welcome to '. APP_CHURCH_NAME . ' online community.';
    	 $mailer 		= new Point_Object_Mail();
    	 $mailer->sendMail(
    	 		array($configs->app->registration->welcome->email, $configs->app->registration->welcome->emailname),
    	 		array($user_info['email'], $user_info['firstname'] . ' '. $user_info['lastname'] ),
    	 		$message,
    	 		$subject
    	 		);
    	 
    	return array( true ,'successful');
    }
    
    /**
     * Finds user(s) by name
     * @param string $name The name to search.
     * 
     * @return Array matching names
     */
    public function findByName($name, $remove_password_info = true)
    {
    	if (null !== $name)
    	{
	    	$db = $this->getUserDbTable();
	    	
	    	$db_adapter	= $db->getAdapter();
	    	
	    	$where		= $db_adapter->quoteInto('MATCH (user_fname, user_lname) AGAINST ( ? IN BOOLEAN MODE )' , '+'. $name);
	    	
	    	$results	= $db_adapter->select()
	    					->from(array('ut'			=>  'users_table'))
	    					->joinLeft(array('uit' 		=> 'users_info_table'), 				'ut.user_id = uit.user_id')
	    					->joinInner(array('st'=>'states_table'), 'st.state_id = uit.user_state_id'	)
							->joinInner(array('ct'=>'countries_table'), 'ct.country_id = uit.user_country_id'	)
//	    					->joinLeft(array('pcgmt' 	=> 'page_content_group_members_table'), 'pcgmt.user_id = ut.user_id')
//	    					->joinLeft(array('pcgt' 	=> 'page_content_groups_table'), 		'pcgt.group_id = pcgmt.page_content_group_id')
	    					
	    					->where($where)->query()->fetchAll();
	    	if ($remove_password_info)
	    	{
	    		foreach($results as &$result)
	    		{
		    		unset_key($result, 'password');	
		    		unset_key($result, 'password_reset');	
		    		unset_key($result, 'active');
	    		}	
	    	}
	    	
	    	return $results; 
    	}
    }
    
    
	public function updateUser($user_info)
	{
		$hasPassword = ( null === $user_info['password'])? true : false;
			
    	if(!$noerror = $this->_infoComplete($user_info, $hasPassword))
    	{
    		return array ((bool)$noerror, 'Incomplete User info');
    	}
    	
		/* Update the user profile */    	 
		
		if(!array_key_exists('user_role', $user_info))
			$user_info['user_role'] = self::MEMBER;
		
		if(!array_key_exists('address2', $user_info))
			$user_info['address2'] = 'N/A';
		
		if(!array_key_exists('zip', $user_info))
			$user_info['zip'] = 'N/A';		
		
		if(!array_key_exists('mobile', $user_info))
			$user_info['mobile'] = 'N/A';
		/**
		 * ---------------
		 * Insert the basic info
		 */
		
		/* get id from email */
		$id = $this->getUserId();//getUserIdByEmail($user_info['email']);
		if(null != $user_info['oldpassword']){
			
			$pwd = $this->getUserDbTable()->select()->from(array('users_table', 'password'))->where('user_id = ?', $id)->query()->fetch();
			
			if (sha1($user_info['oldpassword']) != $pwd['password'])
			{
				return array(false, 'User denied. Invalid password given'.'<br />'.$pwd);
			}
		}
		/* user table */		
    	
    	//	user_id	username	password	email	user_role	active	reg_date
    	
    	$userAdapter = new Zend_Db_Table($this->_getTableName());//$this->getUserDbTable();
    	$user_where  = $userAdapter->getAdapter()->quoteInto('user_id = ? ' , $id );
    	$user_data   = array(
    					'email' 		=> $user_info['email'],
    					'user_fname' 	=> $user_info['firstname'],
    					'user_lname' 	=> $user_info['lastname'],
    					'user_role' 	=> $user_info['user_role']);
    	if ($hasPassword)
    		$user_data['password'] = sha1($user_info['password']);
    	
       	if(($ret = $userAdapter->update($user_data, $user_where))===false)
    	{
    		return array (false, 'Unable to save user info');
    	}
    	
		
    	/**
    	 * ------------
    	 * Insert other info
    	 */
    	 $dob = date( 'Y-m-d H:i:s', strtotime($user_info['day'] . '-' . $user_info['month'] . '-' . $user_info['year'] ) );
    	 
    	 
    	 //user_info_Id`, `user_country_id`, `user_state_id`, `user_address`, `user_address2`, `user_zip`, `user_town`, `user_mobile`
    	 // user_info_Id	user_country_id	user_id	user_state_id	user_date_of_birth	user_gender	user_address	user_address2	user_profile_image	user_zip	user_town	user_mobile
    	 
    	 $info_db = new Zend_Db_Table('users_info_table');
    	 $where = 'user_id = '. $id; //$info_db->getAdapter()->qouteInto('user_id = ?', $id);
    	 $UserInfo_row = array(
    	 				'user_id'			=> $id,
    	 				'user_gender'		=> ($user_info['gender'] == 1 ? 'male' : 'female'),
    	 				'user_address'  	=> $user_info['address1'],
    	 				'user_address2'		=> $user_info['address2'],
    	 				'user_date_of_birth'=> $dob,
    	 				'user_mobile' 	 	=> $user_info['mobile'],
    	 				'user_zip' 	 		=> $user_info['zip'],
    	 				'user_town' 	 	=> $user_info['town'],
    	 				'user_state_id' 	=> $user_info['state'],
    	 				'user_country_id' 	=> $user_info['country']);
    	 if(($ret2 = $info_db->update($UserInfo_row, $where))===false)
    	 {	
    		return array (false, 'Failed to save user info');
    	 }
    	 
    	 $ret = $ret | $ret2;
    	 
		if (0 === $ret) /* Number of affected rows */
    		return array(true, 'Account unchanged');
    	else
	    	return array( true ,'successful');
    }
    
    /**
     * Alias >> getCurrentUserid()
     * This retrieves the current user's id
     */
    public function getUserId()
    {
    	return $this->getCurrentUserid();
    }
	
    /**
     * This retrieves the current user's id using email
     */
    public function getUserIdByEmail($email)
    {
    	$validator = new Zend_Validate_EmailAddress();
    	if($validator->isValid($email))
    	{
    		$result = $this->getUserDbTable()->select()
    									  ->from($this->_getTableName(), array('user_id'))
    									  ->where('email = ?', $email, 'VARCHAR')
    									  ->query()->fetch();
    		if($result) return $result['user_id'];
    	}
    }
	
    /**
     * This retrieves the user by id
     */
    public function getUserById($id)
    {
	    $result = $this->getUserDbTable()->select()
    									  ->where('user_id = ?', (int)$id, 'INT')
    									  ->query()->fetch();
    	if($result) return $result;
    }
	
	/**
	 * @method string Get's current user's role
	 * @return string role
	 */
	public function getRole()
	{
		/* let's try to retrieve it */
		if (null === $this->_user_role)
		{	
			$auth  = Zend_Auth::getInstance();
			if ($auth->hasIdentity()) {
				$identityRow = (array)$auth->getStorage()->read();
				
				// Store the identity insite our class properties
				$this->storeInfo($identityRow);
			}else
				$this->_user_role = self::GUEST;
		}
		return $this->_user_role;
	}
	
	public function getRoleNum()
	{
		if (null === $this->_user_role_num)
		{
			switch ($this->getRole()) // convert  role into numerics
			{
				case Point_Model_User::MEMBER:
					$access = 2;		
				break;
				case Point_Model_User::ADMIN:
					$access = 3;		
				break;
				case Point_Model_User::SUPERADMIN:
					$access = 4;		
				break;
				default:	// guest
					$access = 1;		
			}
			$this->_user_role_num = $access;
		}
		return $this->_user_role_num;
	}
	
	
	/**
	 *  Retrieves first name 
	 */
	public function getFirstname()
	{
		if($this->isLoggedIn())
			return $this->_user_fname;
	}

	
	/**
	 *  Retrieves last name 
	 */
	public function getLastname()
	{
		if($this->isLoggedIn())
			return $this->_user_lname;
	}

	public function getCurrentUserid()
	{
		$auth = Zend_Auth::getInstance();
		
		if ($auth->hasIdentity())
		{
			$info = $auth->getStorage()->read();
			
			return (int)$info['user_id'];	
		}
		return 0;
	}
	
	/**
	 *  This does the authentication by Id
	 * 
	 * If $storeInfo is set, it stores the information of the user
	 * 
	 * @param Numeric $id	
	 * @param boolean stroeInfo
	 */
	public function authenticateById( $id, $storeInfo = true)
	{
		if (null !== $id && is_numeric($id))
		{
			$authAdapter = $this->_getAuthAdapter();
			
			$retrieveUser = $this->getUser($id, false);
			
			/* perform authentication */
			$authAdapter->setIdentity($retrieveUser['email'])
		 				->setCredential($retrieveUser['password']);
		 				
	 			
	 		$auth			= Zend_Auth::getInstance();
	 		$result 		= $auth->authenticate($authAdapter);
	 		
	 		
	 		if($result->isValid())
	 		{
	 			$identity		= (array)$authAdapter->getResultRowObject(
//			 									array('user_id','username','email',
//													  'user_role','active','reg_date')
													  ); // get the current row only
				if (array_key_exists('password', $identity))
					unset($identity['password']);
				
	 			if($storeInfo)
	 			{
	 				/* Store the identity */
	 				$auth->getStorage()->write($identity);
	 				$this->storeInfo($identity);
	 			}
	 			return $identity;
	 		}
	 	}
		
	}
	
	/**
	 *  This does the authentication
	 * 
	 * If $storeInfo is set, it stores the information of the user
	 * 
	 * $userInfo
	 * 		array ('identity' 	=> ? ,
	 * 			   'credential'	=> ? ) 
	 * */
	public function authenticate(array $userInfo, $storeInfo = false)
	{
		
		if (!empty($userInfo))
		{
			$authAdapter = $this->_getAuthAdapter();
			
			$username = $password = null;
			if(array_key_exists('email', $userInfo))
				$username = $userInfo['email'];
			if(array_key_exists('password', $userInfo))
				$password = $userInfo['password'];
//			echo print_r($userInfo, true); exit;
			/////////////////////////
			
			/* perform authentication */
			$authAdapter->setIdentity($username)
		 				->setCredential(SHA1($password))
		 				/*->setCredentialTreatment('SHA1()')*/;
	 			
	 		$auth			= Zend_Auth::getInstance();
	 		$result 		= $auth->authenticate($authAdapter);
			 			
	 		if($result->isValid())
	 		{
	 			$identity		= (array)$authAdapter->getResultRowObject(
//			 									array('user_id','username','email',
//													  'user_role','active','reg_date')
													  ); // get the current row only
				if (array_key_exists('password', $identity))
					unset($identity['password']);
				
	 			if($storeInfo)
	 			{
	 				/* Store the identity */
	 				$auth->getStorage()->write($identity);
	 				$this->storeInfo($identity);
	 			}
	 			return $identity;
	 		}
	 	}
	}
	
	public function storeInfo(array $info)
	{
		if (!empty($info))
		{
			foreach ($info as $var => $value)
			{
				$member	= '_'. $var;
				if (array_key_exists($member, get_class_vars(get_class($this))))
				{
					$this->$member = $value; 		
				}
			}
		}
		return $this; // return self
	}
	
	/**
	 * 
	 */
	public function getUser( $user_id, $no_password = true )
	{
		$result = null;
		if(null === $user_id)
			return null;
			
		if ($result = $this->getUserDbTable()->select()->from(array('u' =>'users_table'))
											 ->where('user_id = ? ', $user_id)->limit(1)->query()->fetch())
		{
			
			/* Remove the password fields if it exists */
			if (is_object($result))
				$result = $result->toArray();
			if(array_key_exists('password', $result) && $no_password)
				unset($result['password']);
			return $result;
		}
		
	}
		
	/**
	 * 
	 */
	public function getUserInfo( $id = null)
	{
		if (null === $id)
		{
			$id 		= $this->getUserId(); 
		}
		
		if (!$id)
			throw new Exception ('Failed: Cannot get info. Invalid user specified');
		if (is_numeric($id))
		{
			$result		= null;
						
			
			$db 	= $this->getUserDbTable()->getAdapter();
			$select = $db->select();
			$select->from(array('ut'=>'users_table'))
			->joinRight(array('uit'=>'users_info_table'), 'ut.user_id = uit.user_id')
			->joinInner(array('st'=>'states_table'), 'st.state_id = uit.user_state_id'	)
			->joinInner(array('ct'=>'countries_table'), 'ct.country_id = uit.user_country_id'	)
			->where('ut.user_id = ?', $id)
			->limit(1);
			$result = $select->query()->fetch();
			
			
			if ($result)
			{
				/* Remove the password fields if it exists */
				unset_key($result, 'password');
			}
			return $result;
		}
	}
	
	public function getAllUsers($retrievePassword = false)
	{
		$adapter = $this->getUserDbTable()->getAdapter();
		/* Create all joins and where statements */
		
		$result = $adapter->select()->from(array('ut'=>'users_table'))
			    ->joinRight(array('uit'=>'users_info_table'), 'ut.user_id = uit.user_id')
			    ->joinInner(array('st'=>'states_table'), 'st.state_id = uit.user_state_id'	)
			    ->joinInner(array('ct'=>'countries_table'), 'ct.country_id = uit.user_country_id'	)
				->query()->fetchAll();
		if (!$retrievePassword)
		{
			if(is_array($result))
				foreach ( $result as &$user ) 
				{
       				unset_key($user, 'password');
				}
		}
		
		return $result;
	}
	
	public function getAll( $where = null)
	{
//		return $this->getUserDbTable()->readAll();
	}
	
	/**
	 * This retrieves the relative profile pix of a user
	 * 
	 * @param numeric id
	 * @param boolean size
	 *  
	 * @return string path/to/profile
	 */
	public function getUserProfileImage($user_id, $small = true)
	{
		/* Ensure we have a valid user ID */
		if (null === $user_id)
			$user_id	= $this->getUserId();
		$db				= $this->getUserDbTable();
		$db_adapter		= $db->getAdapter();
		
		if ($src = $db_adapter->select()->from(array('uit'=> 'users_info_table'))->where('user_id = ?', $user_id)->query()->fetch())
		{
			if ($small)
				return $src[''];
			else
				return $src[''];
		}
	}
	
	/**
	 * Fetch the groups that this user belongs to
	 * 
	 * @return Array User Groups
	 */
	public function getUserGroups()
	{
		
		$groups = array();
		$db 	= $this->getUserDbTable();
		$id 	= $this->getUserId();
		
		if ($id)
		{	
			$groups 	= $db->getAdapter()->select()->from(array('gmt'=>'groups_members_table'))
										   ->joinInner(array('gt'=>'groups_table'), 'gmt.group_id = gt.group_id')
										   ->where('gmt.user_id = ? ', $id)
										   ->query()->fetchAll();
		}	
		/* Automatically append group 0===Public group */
		$public 		= $db->getAdapter()->select()->from(array('gmt'=>'groups_table'))->where('group_privacy_level = ? ', 0)
									  ->query()->fetch();
		$groups[] =  $public;
		
		return $groups;
	}
	
	public function isLoggedIn()
	{
		return Zend_Auth::getInstance()->hasIdentity();
	}
	
	public function fetchAllById($options = null)
	{
		$field =  'user_id'; // Assume primary key
		$value = null;
		
		/* If entry is not array  assume page_id */
		if (!is_array($options))
		{
			$value	=	$options;
		}
		else{
			// Get values from array
			if (array_key_exists('field', $options))
				$field 	= 	$options['field'];	
			if (array_key_exists('value', $options))
				$value 	= 	$options['value'];
		}
		
		/* Read all by the selected query */
		return $this->getUserDbTable()->readAll($field . ' = ?', (int)$value);
	}
	
	public function fullname()
	{
		return $this->user_fname . ' ' . $this->user_lname;
	}
	
	/**
	 *  This points to User::fullname()
	 */
	public function getFullname()
	{
		return $this->fullname();
	}
	
	
	/**
	 * This is used to create the 'Forget Password' message that
	 * the user receives.
	 * 
	 * It parses a PHTML file which may contain php codes
	 * 
	 * @param string hash	This is the authentication code.
	 * @param integer id	User ID.
	 * @param string path	Filepath.
	 * @throws	Exception Invalid file path.
	 */
	protected function _makeForgetPwdMessage($code, $id, $path = null)
	{
		$message = null;
		$config 		= Zend_Registry::get('config');
		$user 			= $this->getUserById($id); 
		$forgot_config = $config->app->password->forgot;
		
		$msg_path 		= $path;
		
		if(null === $path)
		{
			$msg_path = $forgot_config->messagefile;
		}
		if (!readable($msg_path))
			throw new Exception('\'Password Forgot\' message not readable.');
		
		$adminemail 	= $config->app->mail->server->email;
		$churchname	= APP_CHURCH_NAME;
		
		$firstname 	= $user['user_fname'];
		
		$e_id 		= encrypt($id	, APP_SALT);
		$e_c 		= encrypt($code	, APP_SALT);
		
		$terms 		= 'http://'. $config->app->domain.'/about/policy/';
		$link		= 'http://'. $config->app->domain.'/account/forgotpwd/?tu='.urlencode($e_id) . '&t='.urlencode($e_c);
			
		ob_start();
		include($msg_path);
		
		$message = ob_get_contents();
		
		ob_end_clean();
		
		return $message;
	}
	
	
	/**
	 * This is used to create the 'Welcome' message that
	 * the user receives.
	 * 
	 * It parses a PHTML file which may contain php codes
	 * 
	 * @param string hash	This is the activation hash.
	 * @throws	Exception Invalid file path.
	 */
	protected function _makeWelcomeMessage($hash , $path = null)
	{
		$message 		= null;
		$config 		= Zend_Registry::get('config');
		$welcome_config = $config->app->registration->welcome;
		
		$msg_path 		= $path;
		
		if(null === $path)
		{
			
			$msg_path = $welcome_config->messagefile;
		}
		if (!readable($msg_path))
			throw new Exception('Welcome message not readable.');
		
		$adminemail 	= $config->app->mail->server->email;
		$churchname	= APP_CHURCH_NAME;
		$user		= self::getInstance();
		$firstname 	= $user->user_fname;
		$user_info	= $user->getUserInfo();
		$id 		= encrypt($user->getUserId(), APP_SALT);
		$h 			= encrypt($hash				, APP_SALT);
		$terms 		= 'http://'. $config->app->domain.'/about/policy/';
		$link		= 'http://'. $config->app->domain.'/account/activate/?ut='.urlencode($id) . '&u='.urlencode($h);
			
		ob_start();
		include($msg_path);
		
		$message = ob_get_contents();
		
		ob_end_clean();
		
		return $message;	
	}
	
	public function activate($user_id, $activation_code)
	{
		$msg = null;
		/* Search for user */
		$adapter = new Zend_Db_Table('users_table');
		$where = $adapter->getAdapter()->quoteInto('user_id = ?', (int)$user_id);
		
		$search  = $adapter->select()->where($where)->query()->fetch();
		if(!empty($search))
		{

			if(!$search['active'])
			{
					$msg = 'Thank you. <br /><br />Your account appears to have been verified already.';
					$this->logout(false); 						/* log old user out */
					$this->authenticate(array('email'=> $search['email'], 'password'=>$search['password']) , true);
					return array(true, $msg);
			}
			else if ($search['active'] == $activation_code)
			{
				
				if (time() - strtotime($search['reg_date']) < (60*60*24*7*2))
				{
					$data = array('active' => null);
					
					if ( ($ret = $adapter->update($data, $where)) === false)
					{
						$msg = 'Error! <br /><br />Updating failed.';
						return array(false, $msg);
					}
					
					/* Thank-you message */
					$msg = 'Thank you. <br /><br />Your account has been successfully verified.';
					$this->logout(false); 						/* log old user out */
					$this->authenticate(array('email'=> $search['email'], 'password'=>$search['password']) , true);
					return array(true, $msg);	
				}
				else
				{
					/* account was left unattended to for more than 2weeks */
					
					$info_db = new Zend_Db_Table('users_info_table');
					$info_db->delete($where); /* delete account from user_info_table first */
					
					$adapter->delete($where); /* delete info from users_table */
					
					/* Error message */
					$msg = 'Error! <br /><br />Your account has been removed due to non-verification for more than 2weeks.';
					return array(false, $msg);
					
				}
				
			}
			/* Error message */
			$msg = 'Error! <br /><br />The activation code appears to be invalid.';
			return array(false, $msg);
		}
		else{
			/* Error message */
			$msg = 'Error! <br /><br />Your account was not found!';
			return array(false, $msg);
		}
	}
	
	public function startPasswordReset($email)
	{
		$adapter = new Zend_Db_Table('users_table');
		$where 	 = $adapter->getAdapter()->quoteInto('email = ?', $email, 'VARCHAR');
		$result  = $adapter->select()->where($where)->query()->fetch();
		if (!empty($result))
		{
			
			/* Send email for new password */
			$mailer = new Point_Object_Mail();
			$message = $subject = null;
			
			$subject = 'Password Reset';
			
			$code 	 = md5($result['password'] . time());
			
			$adapter = new Zend_Db_Table('users_table');
			$id 	 = $result['user_id'];
			$where 	 = $adapter->getAdapter()->quoteInto('user_id = ? ', $id); 
			$adapter->update(array('password_reset'=> $code), $where);
			 
			$message = $this->_makeForgetPwdMessage($code, $id);
					
			$config = Zend_Registry::get('config');
			$mail_info = $config->app->mail->server;
			
			$success = $msg = null;
			list($success, $msg) = $mailer->sendMail(array($mail_info->email, $mail_info->emailname),
									  array($email, $result['user_fname']. ' '. $result['user_lname']),
									  $message, $subject );
			if ($success)
			{
				$msg = '<p>A message has been sent to your box please respond appropriately</p>';
				return array(true, $msg);	
			}
			else
			{
				return array(false, $msg);
			}
			
			
		}else
		{
			$msg = '<strong>Error</strong>: Sorry we don\'t have such address in our family.';
			return array(false, $msg);
			
		}
	}


	public function changePassword($password, $user_id = null)
	{
		if (null === $user_id)
			$user_id = $this->getUserId();

		$adapter = new Zend_Db_Table('users_table');
		$where 	 = $adapter->getAdapter()->quoteInto('user_id = ?' , (int)$user_id); 
		$result =  $adapter->update(array('password'=>$password, 'password_reset'=>null), $where);
		if ($result !== false)
			return true;
	}
		
	public function logout($clearCookie = true)
	{
		Zend_Session::namespaceUnset(APP_SESSION_NAMESPACE);
        Zend_Auth::getInstance()->clearIdentity();
        Point_Model_Page_Cache::getInstance()->getCache()->clean(Zend_Cache::CLEANING_MODE_OLD);
        if ($clearCookie)
        	Point_Object_Cookie::getInstance()->set(array('name'=>APP_COOKIE_NAME, 'duration'=> time()-COOKIE_AGE));
	}
}

 