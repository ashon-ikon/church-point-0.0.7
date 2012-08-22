<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 10, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_ContentGroups
{
	
	/**
	 * Groups Table
	 */
	protected			$_db_table		= null;
	/**
	
	/**
	 * Groups Table name
	 */
	protected			$_table_name 	= 'page_content_groups_table';
	
	
	/**
	/**
	 * @var	public group id
	 */
	protected			$_public_group_id = 1;	/* This is with the assumption that the first group is public group !!!*/

	/**
	 * @var	public group data
	 */
	protected			$_public_group_data = null;
	
	
	/**
	 * =========================
	 * 	GROUP PRIVACY LEVELS
	 */
	const		ACCESS_PRIVILEGED		= 'privileged';
	const		ACCESS_PUBLIC			= 'public';

	/**
	 * Group membership types
	 * ========================
	 */	
	const		GROUP_GUEST					= 'guest';
	const		GROUP_MEMBER				= 'member';
	const		GROUP_EDITOR				= 'editor';
	const		GROUP_MODERATOR				= 'moderator';
	const		GROUP_ADMIN					= 'administrator';
		
	/**
	 * Singleton instance holder
	 */
	private static		$_instance;
	
	
	private function __construct()
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
	
	public function getDbTable()
	{
		if (null === $this->_db_table)
		{
			$db	= new Point_Model_DbTable_DbCore($this->_table_name);
			$this->_db_table = $db->getDBTable();
		}
		return $this->_db_table;
	}
	
	/**
	 * Adds user to group
	 * @param	Integer	user_id
	 * @param	Integer group_id
	 */
	
	public function addMember($user_id, $group_id, $group_access = 'member')
	{
		if (is_numeric($user_id))
		{
			if (is_numeric($group_id))
			{
				$db 	= new Zend_Db_Table('page_content_group_members_table');
				
				/* prepare data */
				$newMember = array(
					'page_content_group_id' => $group_id,
					'user_id'				=> $user_id,
					'access_right'			=> $group_access,
					'join_date'				=> new Zend_Db_Expr('NOW()')					
				);
				
				/* Store data */
				return $db->insert ($newMember);
			}
			else
				throw new Exception('Groups: Group Id invalid. Failed to add new member');
		}
		else
			throw new Exception('Groups: User Id invalid. Failed to add new member');
	}

	/**
	 * This function gets the membership type of a group member
	 * => 'guest | mederator | admin'
	 */
	public function getMembership($user_id, $group_id = 1)
	{
		$membership 	= self::GROUP_GUEST;
		$members_db		= new Zend_Db_Table('page_content_group_members_table');
		/* Check DB */
		$result			= $members_db->getAdapter()->select()
							 ->from(array('gmt' => 'page_content_group_members_table' ), array('access_right'))
							 ->where('user_id = ? ' , $user_id)
							 ->where('page_content_group_id = ? ', $group_id)->limit(1)
							 ->query()->fetch();
		if ($result)
			$membership 	= $result['access_right'];
		
		return $membership;
	}
	
	/**
	 * Guess group ID by keyword
	 * 
	 * This function attempts to get the content group by keyword
	 * 
	 * Callers should endeavour to die/kill with an exception if necessary
	 * 
	 * @param String groupKeyword
	 */
	public function getGroupIdByKeyword($groupKeyword)
	{
		/* DB Table */
		$db_table 		= $this->getDbTable();
		
		$result 		= $db_table->select()->where('group_name LIKE ? ' , '%' .$groupKeyword . '%')
								   ->query()->fetch();
		if (!empty($result))
			return (int) $result['group_id'];
	}
	
	/**
	 * Adds new group
	 * @param array $group_info
	 * 				( 'name'
	 * 				  'privacy'
	 * 				  	)
	 */
	public function addGroup( array $group_info = array())
	{
		if (!empty($group_info))
		{
			/* Add new group */
			$group_db		= $this->getDbTable();
			$group_name 	= $group_privacy = null;
			if (!($group_name 		= getArrayVar($group_info, 'name')))
				throw new Exception('Invalid or empty group name specified.'); 
			
			$group_privacy 	= getArrayVar($group_info, 'privacy', /* default value=> */ 'public') ;
			$group_desc 	= getArrayVar($group_info, 'description', /* default value=> */ '') ;
				

			
			$group_data		= array(
								'group_name' 			=> $group_name,
								'group_desc' 			=> $group_desc,
								'group_privacy_level'	=> $group_privacy
								);
			$insert_id		= $group_db->insert($group_data);
			
			return $insert_id;
		}
	}
	
	/**
	 * Get All Content Groups
	 * 
	 * This method extracts all the available content groups
	 * 
	 * @param string Öptional Where clause 
	 * 
	 * @return array All the content groups
	 */
	public function getAllGroups($where = null, $order = null)
	{
		$db 		= $this->getDbTable();
		$query 		= $db->select();
		
		if (null !== $where )
		{
			$query 	= $query->where($where);
		}
		
		if (null !== $order )
		{
			$query 	= $query->order($order);
		}
		
//		echo $query, '<br />',$order, '99'; exit;
		$allGroups 	= $query->query()->fetchAll();
		
		return $allGroups;
	}
	
	/**
	 * Get all groups user belongs to
	 * 
	 * This method extracts all the groups that current user belongs to.
	 * 
	 * User must be long to the public group at least!!!
	 * 
	 * @param	Integer	User's  id
	 * 
	 * @return	array of all the current user's groups
	 * 			 
	 */
	public function getUserGroups($user_id)
	{
		$db 		= $this->getDbTable()->getAdapter();
		
		$groups		= array(); 	// So array unshift would be happy
		
		$groups		= $db->select()->from(	array('pcgt'	=>	'page_content_groups_table'))
						 ->joinInner(		array('pcgmt'	=>	'page_content_group_members_table'), 'pcgt.group_id = pcgmt.page_content_group_id')
						 ->where('user_id = ?', $user_id, 'INT')->query()->fetchAll();
		/* Ensure we have at least the public group */
		$has_public_group	= false;
		
		$public_group_data 	= $this->getPublicGroup();
		
		if (!empty($groups))
		{
			
			foreach($groups as $group)
			{
				if ($group['group_id'] == $public_group_data['group_id'])
				{
					/* This user is amongst the public group members */
					$has_public_group	= true;
				}
			}
		}
		
		if(!$has_public_group)
		{
			/*Append the public group*/
			array_unshift($groups, $public_group_data);
		}
		
		return 	$groups;
	}
	
	/**
	 * This function retrieves all the members in a group
	 * 
	 */
	public function getMembers($group_id)
	{
		$db		= new Zend_Db_Table('page_content_group_members_table');
		
		return $db->select()->where('page_content_group_id = ?', (int) $group_id)->query()->fetchAll();
	}
	
	
	/**
	 * ----------------------------------------
	 * The first group should be the public ID
	 */
	public function getPublicGroup()
	{
		if (null === $this->_public_group_data)
		{
			$db = $this->getDbTable();
			
			$public_group_data 	=  $db->select()->where('group_id = ?', $this->_public_group_id, 'INT')
											  ->query()->fetch();
			if (!getArrayVar($public_group_data, 'access_right', false))
			{
				$public_group_data['access_right']	= self::GROUP_GUEST;
			}
			
			/* Ensure that we have a public group */
			if (empty($public_group_data))
				throw new Exception('No public group found!');
			
			$this->_public_group_data 	= $public_group_data ;
		}
		
							  
		return $this->_public_group_data;
	}
} 
