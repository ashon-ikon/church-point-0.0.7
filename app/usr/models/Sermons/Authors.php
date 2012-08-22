<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on May 27, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Sermons_Authors
{
	
	/**
	 * Table name
	 */
	protected	$_table_name 	= 'sermons_authors_table';
	
	protected	$_db_table 		= null;
	
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
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
	 * Adds a new speaker profile
	 * 
	 * @abstract	This adds a new speaker's profile to speakers list
	 * 
	 * @param string firstname
	 * @param string lastname
	 * @param string email
	 * @param string image
	 * 
	 * @return boolean success | failure
	 */
	public function addSpeaker ( $firstname, $lastname, $email = null, $image = null)
	{
		$db			= $this->getDbTable(); 
		/* prepare all fields */
		$firstname	= sanitizeText($firstname, true);
		$lastname	= sanitizeText($lastname, true);
		$email		= $email == null ? '' : sanitizeText($email, true);
		$image		= $image == null ? '' : sanitizeText($image, true);
		
		$new_data 	= array('author_firstname' => $firstname,
							'author_lastname'	=> $lastname,
							'author_email'		=> $email == null ? '' : $email,
							'author_image'		=> $image);
		return $db->insert($new_data); 
	}
	
	/**
	 * Updates a speaker's profile
	 * 
	 * @abstract	This updates a new speaker's profile speakers within the list
	 * 
	 * @param int 	 author_id
	 * @param string firstname
	 * @param string lastname
	 * @param string email
	 * @param string image
	 * 
	 * @return boolean success | failure
	 */
	public function updateSpeaker ( $id, $firstname, $lastname, $email = null, $image = null)
	{
		if (is_numeric($id))
		{
			$db			= $this->getDbTable();
			/* prepare all fields */
			$firstname	= sanitizeText($firstname, true);
			$lastname	= sanitizeText($lastname, true);
			$email		= $email == null ? '' : sanitizeText($email, true);
			$image		= $image == null ? '' : sanitizeText($image, true);

			$new_data 	= array('author_firstname' => $firstname,
						'author_lastname'	=> $lastname,
						'author_email'		=> $email == null ? '' : $email,
						'author_image'		=> $image);
			$where		= $db->getAdapter()->quoteInto('sermon_author_id = ?', $id);
			
			return $db->update($new_data, $where);
		}
		else
			throw new Exception('Invalid speaker id specified for updateSpeaker()');
	}
	
	
	/**
	 * This removes an author from the database
	 * 
	 * @param	int	$sermon_author_id	The id of sermon
	 */
	public function removeSpeaker( $author_id )
	{
		$db		= $this->getDbTable();
		
		$where = $db->getAdapter()->quoteInto('sermon_author_id = ?', $author_id, 'INT');
		
		return $db->delete($where);
	}
	
	
	/**
	 * Get a sermon spkeaker
	 * @abstract	Gets a sermon speaker
	 * 
	 * @param int 	$speaker_id ID
	 * 
	 * @return array Speaker info
	 * 
	 */
	public function getSpeakerById($speaker_id)
	{
		/* Db Object */
		$db		= $this->getDbTable();
		$where	= $db->getAdapter()->quoteInto('sermon_author_id = ?', $speaker_id, 'INT');
		
		$query = $db->select()->where($where);
		if ($sermon 	= $query->query()->fetch())
		{
			return $sermon;
		}
	}
	
	/**
	 * Get a list of sermon spkeakers
	 * @abstract	Gets all sermon speakers from within range specified
	 * 
	 * @param int 	$start	beginning of record
	 * @param int 	$amount	upper limit of record
	 * @param string 	$order	ordering clause
	 * 
	 * @return array List of sermons
	 * 
	 */
	public function getAllSpeakers($start = 0, $amount = 10, $order = 'ASC')
	{
		/* Db Object */
		$db		= $this->getDbTable();
		
		$query = $db->select()->order('author_lastname ' . $order)->order('author_firstname ' . $order)->limit($amount, $start);
		$sermons 	= $query->query()->fetchAll();
		
		if ($sermons)
		{
			return $sermons;
		}
	}
}