<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 10, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Groups
{
	
	/**
	 * Groups Table
	 */
	protected			$_db_table		= null;
	/**
	
	/**
	 * Groups Table name
	 */
	protected			$_table_name 	= 'groups_table';
	
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
	 * This function gets the membership type of a group member
	 * => 'guest | mederator | admin'
	 */
	public function getMembership($user_id, $group_id = 1)
	{
		$membership 	= 'guest';
		$members_db		= new Zend_Db_Table('groups_members_table');
		/* Check DB */
		$result			= $members_db->getAdapter()->select()
							 ->from(array('gmt' => 'groups_members_table' ), array('group_access'))
							 ->where('user_id = ? ' , $user_id)
							 ->where('group_id = ? ', $group_id)->limit(1)
							 ->query()->fetch();
		$membership 	= $result['group_access'];
		
		return $membership;
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
			
			if (!($group_privacy 	= getArrayVar($group_info, 'privacy'))) 
				$group_privacy 		= 0;

			
			$group_data		= array(
								'group_name' 			=> $group_name,
								'group_privacy_level'	=> $group_privacy,
								'reg_date'				=> new Zend_Db_Expr('NOW()')
								);
			$insert_id		= $group_db->insert($group_data);
			
			return $insert_id;
		}
	}
	
	
	/**
	 * Adds user to group
	 * @param	Integer	user_id
	 * @param	Integer group_id
	 */
	// group_member_id	group_id	user_id	group_access	last_seen	reg_date
	public function addMember($user_id, $group_id, $group_access = 'member')
	{
		if (is_numeric($user_id))
		{
			if (is_numeric($group_id))
			{
				$db 	= $this->getDbTable();
				
				/* prepare data */
				$newMember = array(
					'group_id' 		=> $group_id,
					'user_id'		=> $user_id,
					'group_access'	=> $group_access,
					'last_seen'		=> new Zend_Db_Expr('NOW()'),
					'reg_date'		=> new Zend_Db_Expr('NOW()')					
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
} 
