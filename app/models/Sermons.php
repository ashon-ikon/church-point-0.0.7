<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 14, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Sermons
{
	
	/**
	 * Table name
	 */
	protected	$_table_name 	= 'sermons_table';
	
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
	 * This adds new sermon
	 * @param String Sermon Content
	 * @param String Sermon Title
	 * @param String Sermon Author name 
	 * @param String Sermon date
	 * @param String Sermon hightlight [Optional]
	 * @param Integer Sermon Audio Id  [Optional]
	 * @param Integer Sermon Video Id  [Optional]
	 */
	public function addNew($title, $content , $author_id , $sermon_date ,$sermon_highlight = null,  $audio_id = null , $video_id = null )
	{
		if ('' === $content)
			throw new Exception('Sermon Content cannot be empty');
		
		/**
		 * ++++++++++++++++++++++++++++++++++++++++++
		 * Clean the entire inputs from magic-slash
		 */
		$title				= cleanSlashes($title); 
		$content			= cleanSlashes($content); 
		$author_id			= cleanSlashes($author_id); 
		$sermon_highlight	= cleanSlashes($sermon_highlight);
		 
		$user 		= Point_Model_User::getInstance();
		/* Create SEO Friendly title */
		$seo_title 	= null;
		$words 		= preg_split('/ +/',$title);
		if (count($words)>0)
		{
			foreach($words as $word )$seo_title .= $word . '-';
		}
		$seo_title = strtolower(rtrim($seo_title, '-'));
		
		$seo_title	= cleanReplace($seo_title, null , array(' ','-'), array('.','?','\\','/'));
		
			
		$data 	= array(
				'sermon_content' 			=> $content,
				'sermon_title' 				=> $title,
				'sermon_seo_title' 			=> $seo_title,
				'sermon_author_id' 			=> $author_id,
				'sermon_highlight' 			=> $sermon_highlight,
				'sermon_date' 				=> date('Y-m-d h:i:s A',strtotime($sermon_date)),
				'sermon_audio_id'			=> $audio_id,
				'sermon_audio_id'			=> $video_id
			);
		$insert_id = $this->getDbTable()->insert($data);
		
		return $insert_id;
	}
	
	/**
	 * Update sermon
	 * @param Integer sermon_id
	 * @param String title
	 * @param String Content
	 * @param String Sermon Author name 
	 * @param String Sermon date
	 * @param String Sermon hightlight [Optional]
	 * @param Integer Sermon Audio Id  [Optional]
	 * @param Integer Sermon Video Id  [Optional]
	 */
	public function updateSermon($sermon_id, $title, $content ,  $author_id, $sermon_date, $sermon_highlight = null ,$audio_id = null , $video_id = null )
	{
		if ('' === $content)
			throw new Exception('Content cannot be empty');
		
		
		/**
		 * ++++++++++++++++++++++++++++++++++++++++++
		 * Clean the entire inputs from magic-slash
		 */
		$title				= cleanSlashes($title); 
		$content			= cleanSlashes($content); 
		$author_id			= cleanSlashes($author_id); 
		$sermon_highlight	= cleanSlashes($sermon_highlight);
		
		
		/* Create SEO Friendly title */
		
		$seo_title 	= null;
		
		$words 		= preg_split('/ +/',$title);
		if (count($words)>0)
		{
			foreach($words as $word )$seo_title .= $word . '-';
		}
		
		$seo_title 	= strtolower(rtrim($seo_title, '-'));
		
		$seo_title	= cleanReplace($seo_title, null , array(' ','-'), array('.','?','\\','/'));

				
		/* Create the recordset */	
		$data 	= array(
				'sermon_content' 			=> $content,
				'sermon_title' 				=> $title,
				'sermon_seo_title' 			=> $seo_title,
				'sermon_author_id' 			=> $author_id,
				'sermon_highlight' 			=> $sermon_highlight,
				'sermon_date' 				=> date('Y-m-d h:i:s A', strtotime($sermon_date)),
				'sermon_audio_id'			=> $audio_id,
				'sermon_audio_id'			=> $video_id
			);
		$where = $this->getDbTable()->getAdapter()->quoteInto('sermon_id = ?', $sermon_id);
		
		/* Try to update */
		$db 	= $this->getDbTable();
		
		return  $db->update($data, $where);
		
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
		$db		= new Zend_Db_Table('sermons_authors_table');
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
	 * Get a list of sermons
	 * @abstract	Gets all sermons from within range specified
	 * 
	 * @param int 	$start	beginning of record
	 * @param int 	$amount	upper limit of record
	 * @param string 	$order	ordering clause
	 * 
	 * @return array List of sermons
	 * 
	 */
	public function getSermons( $start = 0, $amount = 10, $order = 'DESC')
	{
		/* Db Object */
		$db		= $this->getDbTable();
		
		/**
		 * Select audio and media
		 */
		
		$query = $this->getDbTable()->getAdapter()
					  ->select()->from(array('st' => 'sermons_table'))
					  ->joinLeft(array('sat' =>'sermons_authors_table'), 'st.sermon_author_id = sat.sermon_author_id')
					  ->joinLeft(array('at' =>'audios_table'), 'st.sermon_audio_id = at.audio_id')
//					  ->joinLeft(array('vt' =>'videos_table'), 'st.sermon_video_id = at.video_id');

					->order('sermon_date ' .$order)->limit($amount, $start);
						
		$sermons 	= $query->query()->fetchAll();
			
		if ($sermons)
			return $sermons;
	}
	
	/**
	 * Get a list of sermons in month sermon falls
	 * 
	 * @abstract	Gets all sermons from within range month id
	 * 
	 * @param int 	$month	intended month
	 * @param int 	$year	intended year
	 * 
	 * @return array List of sermons
	 * 
	 */
	public function getSermonsInMonth( $month, $year = null)
	{
		/* Db Object */
		$db				= $this->getDbTable();
		if (null === $year)
			$year	= date('Y');
			
		if(null != $month && checkdate($month, 1, $year))
		{
			/* month info */
			$calendar	= Point_Object_Calendar::getInstance();
			 
			$last_day 	= $calendar->getLastDayOfMonth($month, $year);
			
			$begin_date	= sprintf('%d-%d-%d', $year, $month, 1 ); 
			$end_date	= sprintf('%d-%d-%d 12:00:00', $year, $month, $last_day ); 
			
			
			/*Where clause*/
			
			/* We'll inject in stages */
			$part1		= $db->getDBTable()->getAdapter()->quoteInto('sermon_date BETWEEN ? AND ', $begin_date );
			$where		= $db->getDBTable()->getAdapter()->quoteInto($part1 . ' ? ',  $end_date);
			
			/**
			 * Select audio and media
			 */
			
			$query = $this->getDbTable()->getAdapter()
									->select()->from(array('st' => 'sermons_table'))
									->joinLeft(array('sat' =>'sermons_authors_table'), 'st.sermon_author_id = sat.sermon_author_id')
									->joinLeft(array('at' =>'audios_table'), 'st.sermon_audio_id = at.audio_id')
				//					  ->joinLeft(array('vt' =>'videos_table'), 'st.sermon_video_id = at.video_id');
				
									->where($where);
			
			$sermons 	= $query->query()->fetchAll();
			
			if ($sermons)
				return $sermons;
		}
	}
	
	/**
	 * Retrieve article by params
	 * @params
	 * 		file	=> 'id/2011/12/23/blessings-of-the-Lord'
	 * 		like	=> 'year-mm-dd%'
	 * 		orderby	=> 'article_date DESC'
	 */
	public function getSermon($params, $precise = false)
	{
		$like 	= $precise 	= $year = $month	= $day	= $title	=  $file  	= null;
    	
		if(array_key_exists('file', $params))
			$file 			= $params['file'];

		// 	Extract needed params...
		//  foo/bar?f=2011/12/23/blessings-of-the-Lord
		if (null !== $file )
		{			
			$file	= array_map('trim', explode('/', $file));
			if (count($file) == 5)
			{
				$precise 	= true;
				$id			= intval($file[0]);
				$year		= intval($file[1]);
				$month		= intval($file[2]);
				$day		= intval($file[3]);
				$title		= strval($file[4]);
				$title		= strtolower( substr($title, 0 , strpos($title, '-')) );
				
				//$time		= date(strtotime(implode('-',array($year, $month, $day))) + (6))
				$like = $this->getDbTable()->getAdapter()->quoteInto('sermon_date BETWEEN \'1990-01-01\' AND  ? ', implode('-',array($year, $month, $day)) . ' 12:00:00');
				 
			}
		}
		else
			/* We need a valid sermon date  so return*/
			return; 
			
				
		/* any of the parameter is enough to test!! */
		if (!$year)
		{
				$like = $this->getDbTable()->getAdapter()->quoteInto('sermon_date BETWEEN \'1990-01-01\' AND ? ', date('Y-m-d', time() + (60 * 60 * 12))  );
		}
		
		if (!$like)
			$like = $this->getDbTable()->getAdapter()->quoteInto('sermon_date BETWEEN \'1990-01-01\' AND ?', date('Y-m-d', time() + (60 * 60 * 12) )  );

		if (!array_key_exists('orderby', $params))
			$params['orderby'] = 'sermon_date DESC';
		
		
		
		/* Retrict content to group membership */
		
		/**
		 * Select audio and media
		 */
//		echo $like ; exit;
		$query = $this->getDbTable()->getAdapter()
					  ->select()->from(array('st' => 'sermons_table'))
					  ->joinLeft(array('sat' =>'sermons_authors_table'), 'st.sermon_author_id = sat.sermon_author_id')
					  ->joinLeft(array('at' =>'audios_table'), 'st.sermon_audio_id = at.audio_id');
//					  ->joinLeft(array('vt' =>'videos_table'), 'st.sermon_video_id = at.video_id');
		
		if($year)
			$query->where($like);
		
		$query->order($params['orderby'])->limit(1);
		
		
		$result = $query->query()->fetch();
					
					
		return $result;
	}
	
	
	/**
	 * Retirieve article by ID
	 */
	public function getSermonById($id)
	{
		if(is_numeric($id))
		{
			$order_by	= 'sermon_date DESC';
			
					
		/**
		 * Select audio and media
		 */
		
		$query = $this->getDbTable()->getAdapter()
					  ->select()->from(array('st' => 'sermons_table'))
					  ->joinLeft(array('sat' =>'sermons_authors_table'), 'st.sermon_author_id = sat.sermon_author_id')
					  ->joinLeft(array('at' =>'audios_table'), 'st.sermon_audio_id = at.audio_id')
//					  ->joinLeft(array('vt' =>'videos_table'), 'st.sermon_video_id = at.video_id');

					->where('sermon_id = ?', $id)->order($order_by);
						
			$sermon 	= $query->query()->fetch();
			
			
			return $sermon;
		}
	}
	
	/**
	 * This removes a sermon from database
	 * 
	 * @param	int	$sermon_id	The id of sermon
	 */
	public function remove( $sermon_id )
	{
		$db		= $this->getDbTable();
		
		$where = $this->getDbTable()->getAdapter()->quoteInto('sermon_id = ?', $sermon_id, 'INT');
		
		return $db->delete($where);
	}
	
	/**
	 * This returns a permanent link url based on ID
	 */
	public function getSermonUrl($sermon_id)
	{
		if (is_numeric($sermon_id))
		{
			$sermon 	= $this->getSermonById($sermon_id);
			
			// ?f=id/2011/12/23/blessings-of-the-Lord
			$f 		= '?f=';
			$url 	= $f . $sermon_id . '/'.date('Y/m/d', strtotime($sermon['sermon_date'])) . '/'. $sermon['sermon_seo_title'];
			return  $url;
		}
	}
	
	/**
	 * This function gets the previous sermon
	 */
	public function getPrevSermon($sermon_id)
	{
		if (is_numeric($sermon_id))
		{
			$db 		= $this->getDbTable();
			
			$result 	= $db->select()->where('sermon_id < ?' , $sermon_id, 'INT')->order('sermon_date DESC')->limit(1)->query()->fetch();
			
			if ($result)
			{
				return $this->getSermonById($result['sermon_id']);
			}		
		}
	}
	
	/**
	 * This function gets the next sermon
	 */
	public function getNextSermon($sermon_id)
	{
		if (is_numeric($sermon_id))
		{
			$db 		= $this->getDbTable();
			
			$result 	= $db->select()->where('sermon_id > ?' , $sermon_id, 'INT')->order('sermon_date ASC')->limit(1)->query()->fetch();
			
			
			if ($result)
			{
				return $this->getSermonById($result['sermon_id']);
			}		
		}
	}
	
	/**
	 * Checks if user is priviledge to view article and what level.
	 * 
	 * returns false or priviledge level.
	 */
	public function getPriviledge($article_id, $user_id)
	{
		
		
		if (is_numeric($article_id) && is_numeric($user_id))
		{
			/* Retrieve the article */
			if($article 	= $this->getArticleById($article_id, false /* no need for access check */))
			{
				
				$db 		= new Zend_Db_Table('groups_members_table');
				$result 	= $db->select()->where('user_id = ?', $user_id)->where('group_id = ?', $article['article_group_id'])->query()->fetch();

				/* check if user belongs to the article group .. */								
				if ($result)
				{
					return $result['group_access'];
				}

			}
			
		}
		return Point_Model_User::GROUP_GUEST;
	}
	
	/**
	 * This gets the latest article in the database
	 */
	public function getLatestUrl()
	{
		$db_table 	= $this->getDbTable();
		
		$latest 	= $db_table->select()->order('sermon_date DESC')
					  		   ->limit(1)->query()->fetch();
		$sermon_id = $latest['sermon_id'];
	
		return $this->getSermonUrl($sermon_id);  
		
	}
	
	/**
	 * This retrieves the topmost sermons
	 */
	public function getTopSermons($count = 6)
	{
		
		$sermons 	= $this->getSermons(0, $count);	
		
		return $sermons;
		
	}
	
	/**
	 * This retrieves the latest sermons
	 */
	public function getLatestSermon()
	{
		
		$sermon = $this->getSermons(0,1);
		
		return $sermon[0];
		
	}
	
	/**
	 * This function gets speaker from sermons_speaker_table
	 */
	public function getSermonSpeaker($id)
	{
		if (!is_numeric($id))
			throw new Exception ('Sermon Speaker ID must be numeric');
			
		$db	= new Zend_Db_Table('sermons_authors_table');
		
		$speaker 	= $db->select()->where('sermon_author_id = ?', $id, 'INT')->limit(1)->query()->fetch();
		
		if (!empty($speaker))
			return $speaker;				
	}
}