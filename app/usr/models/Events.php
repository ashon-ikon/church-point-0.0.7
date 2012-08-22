<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by 	ashon on Feb 29, 2012
 * 
 * @depends		Point_Object_Calendar
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Events
{
	
	/**
	 * Table name
	 */
	protected	$_table_name 	= 'events_table';
	
	protected	$_db_table 		= null;
	
	/**
	 * @var	int	group id
	 */
	protected	$_content_group_id	= null;

	/**
	 * @var	string	$_event_image
	 */
	protected	$_event_image_root	= null;
	
	/**
	 * @var	int	$_event_image_filename_length
	 */
	protected	$_event_image_filename_length	= 20;
	
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	private function __construct()
	{
		$contentGroup	= Point_Model_ContentGroups::getInstance();
		
		$this->_content_group_id	= $contentGroup->getGroupIdByKeyword('Events');
		
		if (!is_numeric($this->_content_group_id))
			throw new Exception('No valid group id found for Events');	 
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
	 * set Event Root path
	 */
	public function setEventsImageRoot( $path, $relative_from_app_path = false )
	{
		$_path			= $path;
		
		if ($relative_from_app_path)
		{
			$_path		= realpath(APPLICATION_PATH . DS .'..'. DS . 'public' . DS . $_path);	
		}
		
		$this->_event_image_root	= $_path;
	}
	
	public function getEventsImageRoot()
	{
		if (null === $this->_event_image_root)
		{
			$this->_event_image_root		= realpath(APPLICATION_PATH . DS .'..'. DS . 'public' . DS . APP_EVENTS_IMAGES_DIRECTORY );
		}
		
		return $this->_event_image_root;
	}
	
	/**
	 * This retrieves the event info based on ID
	 * 
	 * @param 	int 	Event count start
	 * @param 	int 	Event count
	 * @param 	int 	Event count order
	 * 
	 * @return 	array 	Events' ID
	 */
	public function getEvents( $start = 0, $amount = 10, $order = 'DESC')
	{
		/* Db Object */
		$db		= $this->getDbTable();
		$events	= $db->getAdapter()
						 ->select()->from(array('et'=>'events_table'))
						 ->joinInner(array('gt' => 'groups_table'), 'et.event_group_id = gt.group_id')
						 ->order('event_start_date ' .$order)->limit($amount, $start)
						 ->query()->fetchAll();
		if ( $events)
		{
			return $events;			 	
		}
	}
	
	/**
	 * This retrieves the events by date 
	 * 
	 * @param 	int 	Events' UNIX timestamp
	 * 
	 * @return 	array 	Events' ID
	 */
	public function getEventByDateTime($time)
	{	
		if (is_numeric($time) )
		{
			$event	= $this->getEventsWithinRange($time, $time); 
			if (!empty($event))
				return $event[0];
		}
	}
	
	
	/**
	 * This retrieves the events by date 
	 * 
	 * @param 	int 	Events' month
	 * @param 	int 	Events' day
	 * @param 	int 	Events' year
	 * 
	 * @return 	array 	Events' ID
	 */
	public function getEventsByDate($month, $day, $year = null)
	{
		if (null === $year)
			$year = date('Y');
			
		if (is_numeric($day) && is_numeric($month))
		{
			$date	= strtotime(sprintf('%d-%d-%d', 	$year, 	$month, 	$day )); 
			$result		= $this->getEventsWithinRange($date, $date);
			if (!empty($result))
				return $result;
		}
	}
	
	/**
	 * This retrieves the event info based on ID
	 * 
	 * @param 	int 	Events' ID
	 * 
	 * @return 	array 	Events' ID
	 */
	public function getEvent($event_id)
	{
		if (!is_numeric($event_id))
			throw new Exception('Non numeric event id given');
			
		$db		= $this->getDbTable();
		
		if ($result	= $db->getAdapter()
						 ->select()->from(array('et'=>'events_table'))
						 ->joinInner(array('gt' => 'groups_table'), 'et.event_group_id = gt.group_id')
						 ->where('event_id = ?', $event_id)->query()->fetch()	)
			return $result;
	}
	
	/**
	 * Add Events
	 * 
	 * This adds a new event to the system
	 * 
	 * @param string $event_title
	 * @param string $event_desc
	 * @param string $event_start_date
	 * @param string $event_stop_date
	 * @param string $event_group_id <Public or others>
	 * @param string $event_image Filepath to event's image
	 * 
	 * @return array result of addition	
	 */
	 public function addEvent($event_title, $event_desc, $event_start_date, $event_stop_date, $event_group_id, $event_image = null)
	 {
	 	/* Validate all inputs */
	 	if (!is_string($event_title))
	 		throw new Exception('Event title must be in string form');
	 		
	 	if (!is_string($event_desc))
	 		throw new Exception('Event desc must be in string form');
	 	
	 	$start_time	= strtotime($event_start_date);
	 	$stop_time	= strtotime($event_stop_date);

	 	if (!$result = checkdate( date('n', $start_time), date('j', $start_time), date('Y', $start_time) ) )
	 		throw new Exception('Event start date must be valid<pre>'. $result.'</pre>');
	 		
	 	if (!checkdate(date('n', $stop_time), date('j', $stop_time), date('Y', $stop_time) ))
	 		throw new Exception('Event ending date must be valid');
	 			
	 	/**
	 	 * --------------------
	 	 *  Store the image 
	 	 */
	 	
	 	$final_image_filename	= null;

	 	if (readable($event_image))
	 	{
		 	
		 	/**
		 	 * copy image from given location to our own events/images path
		 	 */
		 	
		 	/* ensure we have directory or else create it */
		 	$events_img_root_path 	= $this->getEventsImageRoot();
		 	
		 	
		 	writable($events_img_root_path) ||
		 		@mkdir($events_img_root_path, 0777, true);
		 		
		 	if (!writable($events_img_root_path))
		 		throw new Exception ('Failed: Unable to create events folder'); 
		 	
		 	$picture_obj		= Point_Model_Picture::getInstance();
		 	
		 	$dest_file		 	= makeSeoString(snipByWords($event_title, $this->_event_image_filename_length));
		 	
		 	$pic_data			= array(
		 							'source'			=> $event_image,
		 							'destination' 		=> $dest_file,
		 							'description' 		=> $event_desc, 
		 							'dest_path'			=> $events_img_root_path, 
		 							'skip_db' 			=> true,
		 							);
		 	
		 	$result	=	$msg	= null;
		 	
		 	list($result, $msg)	= $picture_obj->saveImage($pic_data);
		 	
		 	if (!$result)
		 		throw new Exception ('Failed: Unable to save events image<pre>' . $msg . '</pre>');
		 		
		 	$final_image_filename	= $msg;
	 	}
	 	
	 	/**
	 	 * -------------- 
	 	 * store the event
	 	 */
	 	$rel_filename	= substr($final_image_filename , strlen($this->getEventsImageRoot()) );
	 	
	 	/* Data Row */
	 	$event_data		= array(
	 						'event_title'		=>  $event_title,
	 						'event_desc'		=>  $event_desc,
	 						'event_image'		=>  $rel_filename,
	 						'event_group_id'	=>  $event_group_id,
	 						'event_start_date'	=>  date('Y-m-d H:i:s', strtotime( $event_start_date)),
	 						'event_stop_date'	=>  date('Y-m-d H:i:s', strtotime($event_stop_date ) ),
	 						'creation_date'		=>  new Zend_Db_Expr('NOW()')
	 						);
//	 						echo '<pre>', print_r($event_data, true),'</pre>';exit;
	 	$db				= $this->getDbTable();
	 	
	 	$result 		= $db->insert($event_data);
	 	
	 	if (false !== $result)
	 		return $result;
	 }

	/**
	 * Update Events
	 * 
	 * This updates an event 
	 *  
	 * @param int 		$event_id
	 * @param string 	$event_title
	 * @param string 	$event_desc
	 * @param string 	$event_start_date
	 * @param string 	$event_stop_date
	 * @param string 	$event_group_id <Public or others>
	 * @param string 	$event_image Filepath to event's image
	 * 
	 * @return array result of addition	
	 */
	 public function editEvent($event_id, $event_title, $event_desc, $event_start_date, $event_stop_date, $event_group_id, $event_image = null)
	 {
	 	/* Validate all inputs */
	 	if (!is_numeric($event_id))
	 		throw new Exception('Event id must be numeric for editing');
	 		
	 	if (!is_string($event_title))
	 		throw new Exception('Event title must be in string form');
	 		
	 	if (!is_string($event_desc))
	 		throw new Exception('Event desc must be in string form');
	 	
	 	$start_time	= strtotime($event_start_date);
	 	$stop_time	= strtotime($event_stop_date);

	 	if (!$result = checkdate( date('n', $start_time), date('j', $start_time), date('Y', $start_time) ) )
	 		throw new Exception('Event start date must be valid<pre>'. $result.'</pre>');
	 		
	 	if (!checkdate(date('n', $stop_time), date('j', $stop_time), date('Y', $stop_time) ))
	 		throw new Exception('Event ending date must be valid');
	 		
	 	/* DB Table handle */
	 	$db				= $this->getDbTable();
	 	
	 	/* Ensure event exists in DB */
	 	if (!($old_data	= $db->select()->where('event_id = ?', $event_id)->query()->fetch()))
	 		throw new Exception('Update: <br />Event not found!');
	 			
	 	/**
	 	 * --------------------
	 	 *  Store the image 
	 	 */
	 	
	 	$final_image_filename	= null;

	 	if (readable($event_image))
	 	{
		 	
		 	
		 	/* ensure we have directory or else create it */
		 	$events_img_root_path 	= $this->getEventsImageRoot();
		 	
		 	
		 	writable($events_img_root_path) ||
		 		@mkdir($events_img_root_path, 0777, true);
		 		
		 	if (!writable($events_img_root_path))
		 		throw new Exception ('Failed: Unable to create events folder'); 
		 	
		 	/**
		 	 * Check if image is the same as the previous (in terms of size)
		 	 */
		 	$picture_obj		= Point_Model_Picture::getInstance();
		 	
		 	$old_pic_path		= remove_trailing_slash($events_img_root_path) . DS . $old_data['event_image'];
		 	$old_picture_info	= $picture_obj->getImageInfo($old_pic_path);
		 	
		 	$dest_file		 	= makeSeoString(snipByWords($event_title, $this->_event_image_filename_length));
		 	
		 	$pic_data			= array(
		 							'source'			=> $event_image,
		 							'destination' 		=> $dest_file,
		 							'description' 		=> $event_desc, 
		 							'dest_path'			=> $events_img_root_path, 
		 							'skip_db' 			=> true,
		 							);
		 	
		 	$result	=	$msg	= null;
		 	
		 	list($result, $msg)	= $picture_obj->saveImage($pic_data);
		 	
		 	if (!$result)
		 		throw new Exception ('Failed: Unable to save events image<pre>' . $msg . '</pre>');
		 		
		 	$final_image_filename	= $msg;
	 	}
	 	
	 	/**
	 	 * -------------- 
	 	 * store the event
	 	 */
	 	$rel_filename	= substr($final_image_filename , strlen($this->getEventsImageRoot()) );
	 	
	 	/* Data Row */
	 	$event_data		= array(
	 						'event_title'		=>  $event_title,
	 						'event_desc'		=>  $event_desc,
	 						'event_image'		=>  $rel_filename,
	 						'event_group_id'	=>  $event_group_id,
	 						'event_start_date'	=>  date('Y-m-d H:i:s', strtotime( $event_start_date)),
	 						'event_stop_date'	=>  date('Y-m-d H:i:s', strtotime($event_stop_date ) ),
	 						'creation_date'		=>  new Zend_Db_Expr('NOW()')
	 						);
//	 						echo '<pre>', print_r($event_data, true),'</pre>';exit;
	 	
	 	
	 	$where			= $db->getAdapter()->quoteInto('event_id = ?', $event_id);
	 	
	 	$result 		= $db->update($event_data, $where);
	 	
	 	if (false !== $result)
	 		return $result;
	 }
	
	/**
	 * Remove Events
	 */
	public function removeEvent($event_id)
	{
		$db		= $this->getDbTable();
		
		if (is_numeric($event_id))
		{
			$where	= $db->getAdapter()->quoteInto('event_id = ? ', $event_id);
			
			/* Check if event exists */
			if ($db->select()->where($where)->qoute()->fetch())
			{
				$db->delete($where);
			}
		}	
	}	
	
	 
	/**
	 * Get Events within range
	 * 
	 * @param int 	$start_time
	 * @param int 	$end_time
	 * 
	 * @return array List of events
	 * 
	 */
	public function getEventsWithinRange( $start_time, $end_time = null, $order = 'DESC')
	{
		if (null === $end_time)
			$end_time = date();
		$start_time_array	= getDateArray($start_time);
		$end_time_array		= getDateArray($end_time);
		
		if(is_string($start_time) && checkdate(	$start_time_array['month'], 	$start_time_array['day'], 	$start_time_array['year']))
		{
			$begin_date	= sprintf('%d-%d-%d', 	$start_time_array['year'], 		$start_time_array['month'], $start_time_array['day'] ); 
			$end_date	= sprintf('%d-%d-%d', 	$end_time_array['year'], 		$end_time_array['month'], 	$end_time_array['day'] ); 

			/*Where clause*/
			
			/* We'll inject in stages */
			$db			= $this->getDbTable();
			$part1		= $db->getDBTable()->getAdapter()->quoteInto('event_start_date BETWEEN ? AND ', $begin_date );
			$where		= $db->getDBTable()->getAdapter()->quoteInto($part1 . ' ? ',  $end_date);

			$events 	= $this->getAllEvents($where, 'event_start_date ' . $order);
			
			if ($events)
				return $events;
		}
	}
	
	
	/**
	 * This retrieves the topmost events
	 */
	public function getTopEvents($count = 6)
	{
		$sermons 	= $this->getEvents(0, $count);	
		
		return $sermons;
	}
	
	 
	/**
	 * Get Events this month
	 * 
	 * @param int 	$month	intended month
	 * @param int 	$year	intended year
	 * 
	 * @return array List of events
	 * 
	 */
	public function getEventsInMonth( $month, $year = null)
	{
		if (null === $year)
			$year = date('Y');
		
		if(null != $month && checkdate($month, 1, $year))
		{
			/* month info */
			$calendar	= Point_Object_Calendar::getInstance();
			 
			$last_day 	= $calendar->getLastDayOfMonth($month, $year);
			
			$begin_date	= sprintf('%d-%d-%d', $year, $month, 1 ); 
			$end_date	= sprintf('%d-%d-%d', $year, $month, $last_day ); 
			
			
			/*Where clause*/
			
			/* We'll inject in stages */
			$db			= $this->getDbTable();
			$part1		= $db->getDBTable()->getAdapter()->quoteInto('event_start_date BETWEEN ? AND ', $begin_date );
			$where		= $db->getDBTable()->getAdapter()->quoteInto($part1 . ' ? ',  $end_date);
			
			$events 	= $this->getAllEvents($where);
			
			if ($events)
				return $events;
		}
	}
	
		 
	/**
	 * Get Event By Event ID
	 */
	
	/**
	 * Get All User Events
	 */
	public function getAllEvents( $where= null, $order = ' event_start_date DESC ')
	{
		$db 	= $this->getDbTable();
		
		$result = $db->select();
		
		if (null !== $where)
			$result->where($where);
		
		return $result->order($order)->query()->fetchAll();
	}
	
}